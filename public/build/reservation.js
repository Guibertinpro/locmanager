(self["webpackChunk"] = self["webpackChunk"] || []).push([["reservation"],{

/***/ "./assets/reservation-js.js":
/*!**********************************!*\
  !*** ./assets/reservation-js.js ***!
  \**********************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

/* provided dependency */ var $ = __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js");
$(function () {
  // Set field Arrhes to 30% of price per default
  var priceField = $("#Reservation_price");
  var arrhesField = $("#Reservation_arrhes");
  var leftToPayField = $("#Reservation_leftToPay");
  var arrhes = 0;
  var leftToPay = 0;
  priceField.on('change', function () {
    var price = priceField.val();
    arrhes = price * 0.3;
    arrhesField.val(arrhes);
    leftToPay = price - arrhes;
    leftToPayField.val(leftToPay);
  });

  // Calculate the solde field value when arrhes value change.
  arrhesField.on('change', function () {
    var price = priceField.val();
    arrhes = arrhesField.val();
    leftToPay = price - arrhes;
    leftToPayField.val(leftToPay);
  });
});

/***/ })

},
/******/ __webpack_require__ => { // webpackRuntimeModules
/******/ var __webpack_exec__ = (moduleId) => (__webpack_require__(__webpack_require__.s = moduleId))
/******/ __webpack_require__.O(0, ["vendors-node_modules_jquery_dist_jquery_js"], () => (__webpack_exec__("./assets/reservation-js.js")));
/******/ var __webpack_exports__ = __webpack_require__.O();
/******/ }
]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoicmVzZXJ2YXRpb24uanMiLCJtYXBwaW5ncyI6Ijs7Ozs7Ozs7O0FBQUFBLENBQUMsQ0FBQyxZQUFZO0VBRVo7RUFDQSxJQUFNQyxVQUFVLEdBQUdELENBQUMsQ0FBQyxvQkFBb0IsQ0FBQztFQUMxQyxJQUFNRSxXQUFXLEdBQUdGLENBQUMsQ0FBQyxxQkFBcUIsQ0FBQztFQUM1QyxJQUFNRyxjQUFjLEdBQUdILENBQUMsQ0FBQyx3QkFBd0IsQ0FBQztFQUNsRCxJQUFJSSxNQUFNLEdBQUcsQ0FBQztFQUNkLElBQUlDLFNBQVMsR0FBRyxDQUFDO0VBQ2pCSixVQUFVLENBQUNLLEVBQUUsQ0FBQyxRQUFRLEVBQUUsWUFBTTtJQUM1QixJQUFNQyxLQUFLLEdBQUdOLFVBQVUsQ0FBQ08sR0FBRyxFQUFFO0lBQzlCSixNQUFNLEdBQUlHLEtBQUssR0FBRyxHQUFJO0lBQ3RCTCxXQUFXLENBQUNNLEdBQUcsQ0FBQ0osTUFBTSxDQUFDO0lBQ3ZCQyxTQUFTLEdBQUlFLEtBQUssR0FBR0gsTUFBTztJQUM1QkQsY0FBYyxDQUFDSyxHQUFHLENBQUNILFNBQVMsQ0FBQztFQUMvQixDQUFDLENBQUM7O0VBRUY7RUFDQUgsV0FBVyxDQUFDSSxFQUFFLENBQUMsUUFBUSxFQUFFLFlBQU07SUFDN0IsSUFBTUMsS0FBSyxHQUFHTixVQUFVLENBQUNPLEdBQUcsRUFBRTtJQUM5QkosTUFBTSxHQUFHRixXQUFXLENBQUNNLEdBQUcsRUFBRTtJQUMxQkgsU0FBUyxHQUFJRSxLQUFLLEdBQUdILE1BQU87SUFDNUJELGNBQWMsQ0FBQ0ssR0FBRyxDQUFDSCxTQUFTLENBQUM7RUFDL0IsQ0FBQyxDQUFDO0FBQ0osQ0FBQyxDQUFDIiwic291cmNlcyI6WyJ3ZWJwYWNrOi8vLy4vYXNzZXRzL3Jlc2VydmF0aW9uLWpzLmpzIl0sInNvdXJjZXNDb250ZW50IjpbIiQoZnVuY3Rpb24gKCkge1xuXG4gIC8vIFNldCBmaWVsZCBBcnJoZXMgdG8gMzAlIG9mIHByaWNlIHBlciBkZWZhdWx0XG4gIGNvbnN0IHByaWNlRmllbGQgPSAkKFwiI1Jlc2VydmF0aW9uX3ByaWNlXCIpO1xuICBjb25zdCBhcnJoZXNGaWVsZCA9ICQoXCIjUmVzZXJ2YXRpb25fYXJyaGVzXCIpO1xuICBjb25zdCBsZWZ0VG9QYXlGaWVsZCA9ICQoXCIjUmVzZXJ2YXRpb25fbGVmdFRvUGF5XCIpO1xuICBsZXQgYXJyaGVzID0gMDtcbiAgbGV0IGxlZnRUb1BheSA9IDA7XG4gIHByaWNlRmllbGQub24oJ2NoYW5nZScsICgpID0+IHtcbiAgICBjb25zdCBwcmljZSA9IHByaWNlRmllbGQudmFsKCk7XG4gICAgYXJyaGVzID0gKHByaWNlICogMC4zKTtcbiAgICBhcnJoZXNGaWVsZC52YWwoYXJyaGVzKTtcbiAgICBsZWZ0VG9QYXkgPSAocHJpY2UgLSBhcnJoZXMpO1xuICAgIGxlZnRUb1BheUZpZWxkLnZhbChsZWZ0VG9QYXkpO1xuICB9KTtcblxuICAvLyBDYWxjdWxhdGUgdGhlIHNvbGRlIGZpZWxkIHZhbHVlIHdoZW4gYXJyaGVzIHZhbHVlIGNoYW5nZS5cbiAgYXJyaGVzRmllbGQub24oJ2NoYW5nZScsICgpID0+IHtcbiAgICBjb25zdCBwcmljZSA9IHByaWNlRmllbGQudmFsKCk7XG4gICAgYXJyaGVzID0gYXJyaGVzRmllbGQudmFsKCk7XG4gICAgbGVmdFRvUGF5ID0gKHByaWNlIC0gYXJyaGVzKTtcbiAgICBsZWZ0VG9QYXlGaWVsZC52YWwobGVmdFRvUGF5KTtcbiAgfSk7XG59KTsiXSwibmFtZXMiOlsiJCIsInByaWNlRmllbGQiLCJhcnJoZXNGaWVsZCIsImxlZnRUb1BheUZpZWxkIiwiYXJyaGVzIiwibGVmdFRvUGF5Iiwib24iLCJwcmljZSIsInZhbCJdLCJzb3VyY2VSb290IjoiIn0=