// script for delete_car

function updateCarStatus(carId) {
    if (confirm('คุณแน่ใจหรือว่าต้องการยกเลิกการใช้งานรถคันนี้?')) {
        fetch('update_car_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ car_id: carId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('สถานะการใช้งานรถถูกยกเลิกเรียบร้อยแล้ว');
                window.location.reload();
            } else {
                alert('เกิดข้อผิดพลาดในการยกเลิกการใช้งานรถ');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
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
