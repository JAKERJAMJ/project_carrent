// ดูรูปภาพที่เพิ่ม
function previewImage() {
    var preview = document.getElementById('image-preview');
    var fileInput = document.getElementById('driver_picture');
    
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

// popup Add_driver
function AddDriver() {
    // ดึงองค์ประกอบของป็อปอัพมา
    var popup = document.getElementById("AddDriver");
    // แสดงป็อปอัพ
    popup.style.display = "block";
}

function hideAddDriver() {
    // ดึงองค์ประกอบของป็อปอัพมา
    var popup = document.getElementById("AddDriver");
    // ซ่อนป็อปอัพ
    popup.style.display = "none";
}

// ฟังก์ชันเพื่อกำหนดรูปแบบเบอร์โทรศัพท์
function formatPhoneNumber(input) {
    
    input.value = input.value.replace(/\D/g, '');
    
    if (input.value.length === 10) {
        let formattedPhoneNumber = input.value.slice(0, 3) + '-' + input.value.slice(3, 6) + '-' + input.value.slice(6, 10);
        input.value = formattedPhoneNumber;
    }
}