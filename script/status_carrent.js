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
    var confirmReceiveCarButton = document.getElementById('confirmReceiveCar');
    var confirmReceiveCarForm = document.getElementById('confirmReceiveCarForm');
    var paymentConfirmModal = new bootstrap.Modal(document.getElementById('paymentConfirmModal'));
    var paymentIdSpan = document.getElementById('paymentId');
    var confirmPaymentButton = document.getElementById('confirmPaymentButton');
    var paymentIdInput = document.getElementById('paymentIdInput');

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

    // Initial check
    toggleDriverSelectBox();

    // Event listeners for changes
    driverStatus.addEventListener('change', function () {
        toggleDriverSelectBox();
        enableQRGenButton();
    });

    form.addEventListener('change', enableQRGenButton);

    confirmReceiveCarButton.addEventListener('click', function () {
        confirmReceiveCarForm.submit();
    });

    document.querySelectorAll('.payment-status-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            var selectedPaymentId = this.getAttribute('data-payment-id');
            paymentIdSpan.textContent = selectedPaymentId;
            paymentIdInput.value = selectedPaymentId;
            paymentConfirmModal.show();
        });
    });

    confirmPaymentButton.addEventListener('click', function () {
        document.getElementById('confirmPaymentForm').submit();
    });
});

function setToday() {
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); // January is 0!
    var yyyy = today.getFullYear();

    // Set display and hidden values
    document.getElementById('display_date_return').value = `${dd}/${mm}/${yyyy}`;
    document.getElementById('date_return').value = `${yyyy}-${mm}-${dd}`;
}

function setTimeNow() {
    var now = new Date();
    var hours = String(now.getHours()).padStart(2, '0');
    var minutes = String(now.getMinutes()).padStart(2, '0');

    // Set display and hidden values
    document.getElementById('display_time_return').value = `${hours}:${minutes}`;
    document.getElementById('time_return').value = `${hours}:${minutes}`;
}

function ReturnCar() {
    var returnCarModal = new bootstrap.Modal(document.getElementById('ReturnCarModal'));
    returnCarModal.show();
}

function handleStatusChange() {
    const returnStatus = document.getElementById('return_status').value;
    const dateTimeInputs = document.getElementById('dateTimeInputs');
    
    if (returnStatus !== 'เลือกสถานะรถ') {
        dateTimeInputs.style.display = 'block';
    } else {
        dateTimeInputs.style.display = 'none';
    }
}

