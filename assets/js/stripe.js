// Get API Key
var STRIPE_PUBLISHABLE_KEY = document.getElementById('STRIPE_PUBLISHABLE_KEY').value;

// Create an instance of the Stripe object and set your publishable API key
const stripe = Stripe(STRIPE_PUBLISHABLE_KEY);

let stripeActionUrl = document.getElementById("stripeAction").value;
let cartTotalAmount = document.getElementById("cartTotalAmount").value;
let stripeRedirectUrl = document.getElementById("stripeRedirectUrl").value;



let elements; // Define card elements
const stripePaymentForm = document.querySelector("#stripePaymentForm"); // Select payment form element

// Get payment_intent_client_secret param from URL
const clientSecretParam = new URLSearchParams(window.location.search).get(
    "payment_intent_client_secret"
);

// Check whether the payment_intent_client_secret is already exist in the URL
setProcessing(true);
if(!clientSecretParam){
    setProcessing(false);
	
    // Create an instance of the Elements UI library and attach the client secret
    initialize();
}

// Check the PaymentIntent creation status
checkStatus();

// Attach an event handler to payment form
stripePaymentForm.addEventListener("submit", handleSubmit);

// Fetch a payment intent and capture the client secret
let payment_intent_id;
async function initialize() {
    const { id, clientSecret } = await fetch(stripeActionUrl, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action:'stripe_payment', request_type:'create_payment_intent',cartTotalAmount:cartTotalAmount }),
    }).then((r) => r.json());
	
    const appearance = {
        theme: 'stripe',
        rules: {
            '.Label': {
                fontWeight: 'bold',
                textTransform: 'uppercase',
            }
        }
    };
	
    elements = stripe.elements({ clientSecret, appearance });
	
    const stripePaymentElement = elements.create("payment");
    stripePaymentElement.mount("#stripePaymentElement");
	
    payment_intent_id = id;
}

// Card form submit handler
async function handleSubmit(e) {
    e.preventDefault();
    setLoading(true);

    var first_name = document.getElementById("first_name").value;
    var last_name = document.getElementById("last_name").value;
    var customer_email = document.getElementById("user_email").value;

    var address = document.getElementById("address").value;
    var city = document.getElementById("city").value;
    var state = document.getElementById("state").value;
    var zip_code = document.getElementById("zip_code").value;
    var phone = document.getElementById("phone").value;
    
	
    const { id, customer_id } = await fetch(stripeActionUrl, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action:'stripe_payment', request_type:'create_customer', 
                        payment_intent_id: payment_intent_id, 
                        first_name: first_name,
                        last_name:last_name,
                        email: customer_email,
                        address:address,
                        city:city,
                        state:state,
                        zip_code:zip_code,
                        phone:phone
                    }),
    }).then((r) => r.json());
	
    const { error } = await stripe.confirmPayment({
        elements,
        confirmParams: {
            // Make sure to change this to your payment completion page
            return_url: window.location.href+'?customer_id='+customer_id,
        },
	});
	
    // This point will only be reached if there is an immediate error when
    // confirming the payment. Otherwise, your customer will be redirected to
    // your `return_url`. For some payment methods like iDEAL, your customer will
    // be redirected to an intermediate site first to authorize the payment, then
    // redirected to the `return_url`.
    if (error.type === "card_error" || error.type === "validation_error") {
        showMessage(error.message);
        setLoading(false);
    } else {
        showMessage("An unexpected error occured.");
        setLoading(false);
    }
	
    // setLoading(false);
}

// Fetch the PaymentIntent status after payment submission
async function checkStatus() {
    const clientSecret = new URLSearchParams(window.location.search).get(
        "payment_intent_client_secret"
    );
	
    const customerID = new URLSearchParams(window.location.search).get(
        "customer_id"
    );
	
    if (!clientSecret) {
        return;
    }
	setLoading(true);
    const { paymentIntent } = await stripe.retrievePaymentIntent(clientSecret);
	
    if (paymentIntent) {
        switch (paymentIntent.status) { 
            case "succeeded":
                
                //showMessage("Payment succeeded!");
                var first_name = document.getElementById("first_name").value;
                var last_name = document.getElementById("last_name").value;
                var customer_email = document.getElementById("user_email").value;
            
                var address = document.getElementById("address").value;
                var city = document.getElementById("city").value;
                var state = document.getElementById("state").value;
                var zip_code = document.getElementById("zip_code").value;
                var phone = document.getElementById("phone").value;
                // Post the transaction info to the server-side script and redirect to the payment status page
                fetch(stripeActionUrl, {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ action:'stripe_payment',request_type:'payment_insert',
                                        payment_intent: paymentIntent,
                                        customer_id: customerID,
                                        first_name: first_name,
                                        last_name:last_name,
                                        email: customer_email,
                                        address:address,
                                        city:city,
                                        state:state,
                                        zip_code:zip_code,
                                        phone:phone
                                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.payment_id) {
                        console.log(data);
                        transErr = 0;
                        window.location.href = stripeRedirectUrl+'?pid='+data.payment_id;
                    } else {
                        showMessage(data.error);
                        setReinit();
                        setLoading(false);
                    }
                })
                .catch(console.error);
				
                break;
            case "processing":
                showMessage("Your payment is processing.");
                setReinit();
                setLoading(false);
                break;
            case "requires_payment_method":
                showMessage("Your payment was not successful, please try again.");
                setReinit();
                setLoading(false);
                break;
            default:
                showMessage("Something went wrong.");
                setReinit();
                setLoading(false);
                break;
        }
    } else {
        showMessage("Something went wrong.");
        setReinit();
        setLoading(false);
    }
   // setLoading(false);
}


// Display message
function showMessage(messageText) {
    const messageContainer = document.querySelector("#stripePaymentResponse");
	
    messageContainer.classList.remove("hidden");
    messageContainer.textContent = messageText;
	
    setTimeout(function () {
        messageContainer.classList.add("hidden");
        messageText.textContent = "";
    }, 5000);
}

// Show a spinner on payment submission
function setLoading(isLoading) {
    if (isLoading) {
        // Disable the button and show a spinner
        document.querySelector("#stripePaymentBtn").disabled = true;
        document.querySelector("#spinner").classList.remove("hidden");
        document.querySelector(".receipt-box").classList.add("box_spinner");
        document.querySelector("#buttonText").classList.add("hidden");
    } else {
        // Enable the button and hide spinner
        document.querySelector("#stripePaymentBtn").disabled = false;
        document.querySelector("#spinner").classList.add("hidden");
        document.querySelector(".receipt-box").classList.remove("box_spinner");
        document.querySelector("#buttonText").classList.remove("hidden");
    }
}

// Show a spinner on payment form processing
function setProcessing(isProcessing) {
    if (isProcessing) {
        stripePaymentForm.classList.add("hidden");
       // document.querySelector("#frmProcess").classList.remove("hidden");
    } else {
        stripePaymentForm.classList.remove("hidden");
        //document.querySelector("#frmProcess").classList.add("hidden");
    }
}

// Show payment re-initiate button
function setReinit() {
    //document.querySelector("#frmProcess").classList.add("hidden");
    //document.querySelector("#payReinit").classList.remove("hidden");
}