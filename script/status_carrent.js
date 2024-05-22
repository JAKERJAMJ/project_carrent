document.addEventListener('DOMContentLoaded', function () {
    var driverStatus = document.getElementById('driver_status');
    var driverSelectBox = document.getElementById('driverSelectBox');
    var originalPrice = parseFloat(document.getElementById('original_price').value);
    var updatedPriceField = document.getElementById('updated_price');

    function toggleDriverSelectBox() {
        if (driverStatus.value === 'ต้องการคนขับ') {
            driverSelectBox.style.display = 'block';
            updatedPriceField.value = (originalPrice + 200).toFixed(2);
        } else {
            driverSelectBox.style.display = 'none';
            updatedPriceField.value = originalPrice.toFixed(2);
        }
    }

    // Initial check
    toggleDriverSelectBox();

    // Add event listener for changes
    driverStatus.addEventListener('change', toggleDriverSelectBox);
});