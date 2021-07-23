/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!******************************!*\
  !*** ./resources/js/room.js ***!
  \******************************/
$(function () {
  /*
      let player = videojs('main-video', {
          fluid: true
      });
      player.play();
  */
  getViewCount();
  setInterval(function () {
    getViewCount();
  }, 30000);

  function getViewCount() {
    var url = '/room/count-views';
    var params = {
      data: {
        room_id: $('#room_id').val()
      }
    };
    axios.post(url, params).then(function (response) {
      // 成功したら視聴数の表示を更新
      $('#view-count').html(response.data.views);
    });
  }

  if (window.Laravel.apiToken != null) {
    var stripeTokenHandler = function stripeTokenHandler(token) {
      $.ajax({
        type: 'POST',
        url: '/api/charge?api_token=' + window.Laravel.apiToken,
        data: {
          token_id: token.id,
          charge_amount: $('#charge-amount').val()
        },
        dataType: 'json',
        success: function success(data, dataType) {
          if (data.result == 'OK') {
            location.reload();
          } else {
            cardSubmit.style.display = 'none';
          }
        }
      });
    };

    // 課金処理
    var stripe = Stripe("pk_live_uMaiRJG3F2pCJpFzFuwTilHq008hvobhtm");
    var elements = stripe.elements();
    var style = {
      base: {
        color: '#32325d',
        fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
        fontSmoothing: 'antialiased',
        fontSize: '16px',
        '::placeholder': {
          color: '#aab7c4'
        }
      },
      invalid: {
        color: '#fa755a',
        iconColor: '#fa755a'
      }
    };
    var card = elements.create('card', {
      style: style,
      hidePostalCode: true
    });
    card.mount('#card-element');
    card.addEventListener('change', function (event) {
      var displayError = document.getElementById('card-errors');

      if (event.error) {
        displayError.textContent = event.error.message;
      } else {
        displayError.textContent = '';
      }
    });
    var cardSubmit = document.getElementById('card-submit');
    cardSubmit.addEventListener('click', function (event) {
      event.preventDefault();
      cardSubmit.style.display = 'none';
      stripe.createToken(card).then(function (result) {
        if (result.error) {
          var errorElement = document.getElementById('stripe-err');
          errorElement.textContent = result.error.message;
        } else {
          // Send the token to your server.
          stripeTokenHandler(result.token);
        }
      });
    });
  }
});
/******/ })()
;