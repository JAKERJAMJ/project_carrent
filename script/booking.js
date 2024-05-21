function searchMember(carId) {
    var passport = document.getElementById('Memberpassport').value;
    var startDate = document.getElementById('carrent_date').value; // เก็บค่าวันที่เริ่มต้น
    var endDate = document.getElementById('carrent_return').value; // เก็บค่าวันที่สิ้นสุด
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'booking_page.php?id=' + carId, true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status == 200) {
            var response = JSON.parse(this.responseText);
            if (response.found) {
                document.getElementById('Membername').value = response.memberName;
                document.getElementById('MemberID').value = response.memberID;
                document.getElementById('DetailRent').style.display = 'block';
                document.getElementById('AddRent').style.display = 'block';
                CloseSearchPopup(); // ปิด popup search เมื่อค้นหาเจอแล้ว
            
                // Calculate rental price
                var rentalPricePerDay = response.carPrice; // ราคาเช่าต่อวันจาก response
                var rentalPrice = calculateRentalPrice(startDate, endDate, rentalPricePerDay);
                document.getElementById('carrent_price').value = rentalPrice;
            
                // Show booking-body form
                document.querySelector('.booking-body').style.display = 'block';
            } else {
                alert('ไม่พบข้อมูลผู้เช่า');
            }
            
        }
    };
    xhr.send('search=true&Memberpassport=' + passport + '&start_date=' + startDate + '&end_date=' + endDate + '&car_id=' + carId); // ส่งค่าวันที่เริ่มต้นและสิ้นสุดไปกับข้อมูลการค้นหา
}



function CloseSearchPopup() {
    document.getElementById('Search').style.display = 'none';
}

function OpenSearch() {
    document.getElementById('Search').style.display = 'block';
}

// คำนวนราคาเช่าต่อวัน
function calculateTotal() {
    var rentalDate = document.getElementById("RentalDate").value;
    var returnDate = document.getElementById("ReturnDate").value;

    if (!rentalDate || !returnDate) {
        alert("กรุณาเลือกวันที่เริ่มเช่าและวันที่สิ้นสุดการเช่า");
        return;
    }

    var startDate = new Date(rentalDate);
    var endDate = new Date(returnDate);

    var timeDiff = endDate.getTime() - startDate.getTime();
    var numDays = Math.ceil(timeDiff / (1000 * 3600 * 24));

    if (numDays < 0) {
        alert("วันที่สิ้นสุดการเช่าต้องหลังจากวันที่เริ่มเช่า");
        return;
    }

    var rentalRate = parseFloat(document.getElementById('car_price').value);
    var totalPrice = numDays * rentalRate;

    document.getElementById("RentalPrice").value = totalPrice.toFixed(2);
    document.getElementById("carrent_price").value = totalPrice.toFixed(2);
}

