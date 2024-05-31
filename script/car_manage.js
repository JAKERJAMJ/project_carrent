// script for delete_car

function deleteCar(carId) {
    if(confirm('ต้องการยกเลิกการใช้งานรถ ID ' + carId + '?')) {
    // ส่งคำขอไปยังไฟล์ PHP พร้อมกับ ID ของรถยนต์
        fetch('delete_car.php?id=' + carId, {
            method: 'GET'
        })
    .then(response => response.text())
    .then(data => {
        alert(data); // แสดงข้อความจากการตอบกลับของ PHP
        window.location.reload(); // โหลดหน้าใหม่เพื่ออัพเดทข้อมูล
    })
    .catch(error => console.error('Error:', error));
    }
}


// js script popup
    function showPopup() {
        // ดึงองค์ประกอบของป็อปอัพมา
        var popup = document.getElementById("add_car");
        // แสดงป็อปอัพ
        popup.style.display = "block";
    }

    function hidePopup() {
        // ดึงองค์ประกอบของป็อปอัพมา
        var popup = document.getElementById("add_car");
        // ซ่อนป็อปอัพ
        popup.style.display = "none";
    }

    // script preview img

function previewImage() {
    var preview = document.getElementById('image-preview');
    var fileInput = document.getElementById('main_picture');
    
    if (fileInput.files && fileInput.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(fileInput.files[0]); // อ่านไฟล์เป็น URL ข้อมูล
    } else {
        preview.src = "";
        preview.style.display = null;
    }
}
