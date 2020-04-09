/* 基本設定*/

const stripe = Stripe(stripe_public_key);
const elements = stripe.elements();

/* Stripe Elementsを使ったFormの各パーツをどんなデザインにしたいかを定義 */
const style = {
    base: {
        fontSize: '12px',
        color: "#32325d",
        border: "solid 1px ccc"
    }
};

const classes = {
    base: "form-control"
};

/* フォームでdivタグになっている部分をStripe Elementsを使ってフォームに変換 */
const cardElement = elements.create('card', {style:style,classes:classes, hidePostalCode: true});
cardElement.mount('#cardNumber');

// form submit
document.addEventListener("DOMContentLoaded", function() {
    
    const cardHolderName = document.getElementById('cardName');
    const cardButton = document.getElementById('card-button');
    const clientSecret = cardButton.dataset.secret;
    const form = document.getElementById('form_payment');

    cardButton.addEventListener('click', async (e) => {
console.log('hgoehogehoge');
        const { setupIntent, error } = await stripe.confirmCardSetup(
            clientSecret, {
                payment_method: {
                    card: cardElement,
                    billing_details: { name: cardHolderName.value }
                }
            }
        );

        if (error) {
            // ユーザーに"error.message"を表示する…
        } else {
            const form = document.getElementById('form_payment');
            const hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'payment_method');
            hiddenInput.setAttribute('value', setupIntent.payment_method);
            form.appendChild(hiddenInput);

            form.submit();
        }
    });
}, false);