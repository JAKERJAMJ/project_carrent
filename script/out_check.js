function checkUserLogin(car_id, startDate, endDate) {
    if (!userIsLoggedIn) {
        var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
        loginModal.show();
    } else {
        window.location.href = 'booking_out.php?car_id=' + car_id + '&start_date=' + startDate + '&end_date=' + endDate;
    }
}
