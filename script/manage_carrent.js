function ShowAddDetailPopup() {
    document.getElementById('Search').style.display = 'none';
    document.getElementById('AddDetail').style.display = 'block';
}

function CloseSearchPopup() {
    document.getElementById('Search').style.display = 'none';
}

function CloseCarrentPopup() {
    document.getElementById('AddDetail').style.display = 'none';
}


function Carrent() {
    var popup = document.getElementById('Search');

    popup.style.display = 'block';
}


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

function CheckDate() {
    var popup = document.getElementById('CheckDate');

    popup.style.display = 'block';
}
function CloseCheckPopup(event) {
    var popup = document.getElementById('CheckDate');
    var clickedElement = event.target;

    // ตรวจสอบว่าองค์ประกอบที่คลิกไม่ใช่ปุ่มที่เกี่ยวข้องกับการค้นหา รวมถึงปุ่มค้นหาและองค์ประกอบในฟอร์ม
    if (!clickedElement.closest('.check-form') && clickedElement.type !== 'submit' && clickedElement.type !== 'date' && clickedElement.name !== 'CheckDate') {
        popup.style.display = 'none';
    }
}




