document.addEventListener('DOMContentLoaded', function() {
    var driverStatus = document.getElementById('driver_status');
    var driverSelectBox = document.getElementById('driverSelectBox');
    var originalPrice = parseFloat(document.getElementById('original_price').value);
    var rentalPriceField = document.getElementById('RentalPrice');
    var driverDailyWage = parseFloat(document.getElementById('driver_daily_wage').value);
    var rentalDays = parseInt(document.getElementById('rental_days').value);
    var totalDriverCost = rentalDays * driverDailyWage;

    driverStatus.addEventListener('change', function() {
        if (driverStatus.value === 'ต้องการคนขับ') {
            driverSelectBox.style.display = 'block';
            rentalPriceField.value = (originalPrice + totalDriverCost).toFixed(2);
        } else {
            driverSelectBox.style.display = 'none';
            rentalPriceField.value = originalPrice.toFixed(2);
        }
    });

    var confirmButton = document.getElementById('confirmButton');
    confirmButton.addEventListener('click', function() {
        window.location.href = 'payment.php?rent_id=<?= $rentID ?>';
    });

    var successModal = document.getElementById('successModal');
    successModal.addEventListener('hidden.bs.modal', function() {
        window.location.href = 'payment.php?rent_id=<?= $rentID ?>';
    });
});

function validateForm() {
    var rentalTime = document.getElementById('RentalTime').value;
    var returnTime = document.getElementById('ReturnTime').value;

    if (!rentalTime || !returnTime) {
        alert('กรุณาระบุเวลาในการรับรถและคืนรถ');
        return false;
    }
    return true;
}