// ฟังก์ชันเพื่อกำหนดรูปแบบเบอร์โทรศัพท์
function formatPhoneNumber(input) {
    
    input.value = input.value.replace(/\D/g, '');
    
    if (input.value.length === 10) {
        let formattedPhoneNumber = input.value.slice(0, 3) + '-' + input.value.slice(3, 6) + '-' + input.value.slice(6, 10);
        input.value = formattedPhoneNumber;
    }
}

// เมื่อคลิกที่ปุ่ม "แก้ไขรูปภาพ"
document.getElementById("edit-btn").addEventListener("click", function() {
    // ซ่อนปุ่ม "แก้ไขรูปภาพ"
    this.style.display = "none";
    // แสดงฟอร์มอัปโหลดรูปภาพและปุ่มอัพเดต
    document.getElementById("upload-form").style.display = "block";
    // แสดง input ไฟล์
    document.getElementById("profile_pic").style.display = "block";
});

// function ของการเปลี่ยนรหัสผ่าน
function checkPasswordMatch() {
    let password = document.getElementById("PasswordOld").value;
    let confirmPassword = document.getElementById("EnterPassword").value;
    let errorDiv = document.getElementById("passwordError");

    if (password !== confirmPassword) {
        errorDiv.innerHTML = "รหัสผ่านไม่ถูกต้อง";
        errorDiv.style.color = "red"; // เปลี่ยนสีข้อความเป็นสีแดง
    } else {
        errorDiv.innerHTML = "รหัสผ่านถูกต้อง";
        errorDiv.style.color = "green"; // เปลี่ยนสีข้อความเป็นสีเขียว
    }
}
// เรียกใช้ฟังก์ชัน checkPasswordMatch ทุกครั้งที่พิมพ์ใน input
document.getElementById("EnterPassword").addEventListener("input", checkPasswordMatch);
