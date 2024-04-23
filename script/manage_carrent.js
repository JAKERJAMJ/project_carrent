function searchMember() {
    var passport = document.getElementById('Memberpassport').value;
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'manage_carrent.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status == 200) {
            var response = JSON.parse(this.responseText);
            if (response.found) {
                document.getElementById('Membername').value = response.memberName;
                document.getElementById('MemberID').value = response.memberID;
                document.getElementById('DetailRent').style.display = 'block'; // แสดงรายละเอียด
                document.getElementById('AddRent').style.display = 'block'; // รักษาป๊อปอัพให้เปิดอยู่
            } else {
                alert('ไม่พบข้อมูลผู้เช่า');
            }
        }
    };
    xhr.send('search=true&Memberpassport=' + passport);
}

function Carrent() {
    document.getElementById("AddRent").style.display = "block";
}

function CloseCarrent() {
    document.getElementById("AddRent").style.display = "none";
}




function updateRentalRate() {
    var select = document.getElementById("car_id");
    var selectedOption = select.options[select.selectedIndex];
    rentalRate = parseFloat(selectedOption.getAttribute('data-rate')); // ต้องใช้ชื่อ attribute ที่ถูกต้อง
    calculateTotal(); // คำนวณราคาเช่าใหม่ทันที
}


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

    if (numDays === 0) numDays = 1; // ถ้าคืนรถในวันเดียวกัน คิดเป็น 1 วัน

    var totalPrice = numDays * rentalRate;
    document.getElementById("RentalPrice").value = totalPrice.toFixed(2);
}
