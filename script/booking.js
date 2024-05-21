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
            } else {
                alert('ไม่พบข้อมูลผู้เช่า');
            }
        }
    };
    xhr.send('search=true&Memberpassport=' + passport + '&start_date=' + startDate + '&end_date=' + endDate); // ส่งค่าวันที่เริ่มต้นและสิ้นสุดไปกับข้อมูลการค้นหา
}




function CloseSearchPopup() {
    document.getElementById('Search').style.display = 'none';
}

function OpenSearch() {
    document.getElementById('Search').style.display = 'block';
}
