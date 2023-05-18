$(function () {

  // Set field Arrhes to 30% of price per default
  const priceField = $("#Reservation_price");
  const arrhesField = $("#Reservation_arrhes");
  const leftToPayField = $("#Reservation_leftToPay");
  let arrhes = 0;
  let leftToPay = 0;
  priceField.on('change', () => {
    const price = priceField.val();
    arrhes = (price * 0.3);
    arrhesField.val(arrhes);
    leftToPay = (price - arrhes);
    leftToPayField.val(leftToPay);
  });

  // Calculate the solde field value when arrhes value change.
  arrhesField.on('change', () => {
    const price = priceField.val();
    arrhes = arrhesField.val();
    leftToPay = (price - arrhes);
    leftToPayField.val(leftToPay);
  });
});