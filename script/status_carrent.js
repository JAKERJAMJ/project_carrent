document.addEventListener('DOMContentLoaded', function () {
    var driverStatus = document.getElementById('driver_status');
    var driverSelectBox = document.getElementById('driverSelectBox');
    var driverSelect = document.getElementById('driver_id');
    var originalPrice = parseFloat(document.getElementById('original_price').value);
    var updatedPriceField = document.getElementById('updated_price');
    var driverDailyWage = parseFloat(document.getElementById('driver_daily_wage').value);
    var rentalDays = parseInt(document.getElementById('rental_days').value);
    var totalDriverCost = rentalDays * driverDailyWage;
    var qrgenButton = document.getElementById('qrgen');
    var form = document.getElementById('statusForm');

    function toggleDriverSelectBox() {
        if (driverStatus.value === 'ต้องการคนขับ') {
            driverSelectBox.style.display = 'block';
            updatedPriceField.value = (originalPrice + totalDriverCost).toFixed(2);
        } else {
            driverSelectBox.style.display = 'none';
            updatedPriceField.value = originalPrice.toFixed(2);
            driverSelect.value = '5'; // Set driver_id to 5
        }
    }

    function enableQRGenButton() {
        qrgenButton.disabled = false;
    }

    // ตรวจสอบครั้งแรก
    toggleDriverSelectBox();

    // เพิ่ม event listener สำหรับการเปลี่ยนแปลง
    driverStatus.addEventListener('change', function () {
        toggleDriverSelectBox();
        enableQRGenButton();
    });

    form.addEventListener('change', enableQRGenButton);
});
