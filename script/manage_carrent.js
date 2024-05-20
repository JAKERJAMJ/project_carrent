// คำนวนราคาในการเช่ารถ
function updateRentalRate() {
    var carId = document.getElementById('car_id').value;
    var rentalDate = document.getElementById('carrent_date').value;
    var returnDate = document.getElementById('carrent_return').value;

    if (carId !== 'เลือกรถที่ต้องการเช่า' && rentalDate && returnDate) {
        var rate = document.getElementById('car_id').options[document.getElementById('car_id').selectedIndex].getAttribute('data-rate');
        var rentalDays = 1; // Set to 1 day if rental date and return date are the same
        if (rentalDate !== returnDate) {
            rentalDays = Math.ceil((new Date(returnDate) - new Date(rentalDate)) / (1000 * 3600 * 24));
        }
        var rentalPrice = parseInt(rate) * rentalDays;
        document.getElementById('carrent_price').value = rentalPrice;
    } else {
        document.getElementById('carrent_price').value = '';
    }
}



// function การลบ/ยกเลิกการจอง

// JavaScript function to cancel a car rental
function cancelCarRental(carRentID) {
    if (confirm("คุณแน่ใจหรือไม่ที่จะยกเลิกการเช่ารถนี้?")) {
        // If user confirms the cancellation, send the car rent ID to PHP script for deletion
        window.location.href = "cancel_rental.php?carrent_id=" + carRentID;
    }
}

function Carrent() {
    CheckDate();
}


function CloseCheckPopup(event) {
    var popup = document.getElementById('CheckDate');
    var clickedElement = event.target;

    // ตรวจสอบว่าองค์ประกอบที่คลิกไม่ใช่ปุ่มที่เกี่ยวข้องกับการค้นหา รวมถึงปุ่มค้นหาและองค์ประกอบในฟอร์ม
    if (!clickedElement.closest('.check-form') && clickedElement.type !== 'submit' && clickedElement.type !== 'date' && clickedElement.name !== 'CheckDate') {
        popup.style.display = 'none';
    }
}

function CheckDate() {
    // ปิด popup ที่อาจเปิดอยู่ก่อนหน้านี้
    CloseCheckPopup(event);
    
    var popup = document.getElementById('CheckDate');
    popup.style.display = 'block';
}

function Submit() {
    document.getElementById('CheckDate').style.display = 'block'; // แสดง popup CheckDate เมื่อคลิกปุ่มค้นหา
}






