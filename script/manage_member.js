// ฟังก์ชันเพื่อกำหนดรูปแบบเบอร์โทรศัพท์
function formatPhoneNumber(input) {
    
    input.value = input.value.replace(/\D/g, '');
    
    if (input.value.length === 10) {
        let formattedPhoneNumber = input.value.slice(0, 3) + '-' + input.value.slice(3, 6) + '-' + input.value.slice(6, 10);
        input.value = formattedPhoneNumber;
    }
}


// function ตรวจสอบ Password
// ฟังก์ชันเพื่อตรวจสอบการยืนยันพาสเวิร์ดทันที
function checkPasswordMatch() {
    let password = document.getElementById("Memberpassword").value;
    let confirmPassword = document.getElementById("confirmPassword").value;
    let errorDiv = document.getElementById("passwordError");

    if (password !== confirmPassword) {
        errorDiv.innerHTML = "รหัสผ่านไม่ตรงกัน";
        errorDiv.style.color = "red"; // เปลี่ยนสีข้อความเป็นสีแดง
    } else {
        errorDiv.innerHTML = "รหัสผ่านตรงกัน";
        errorDiv.style.color = "green"; // เปลี่ยนสีข้อความเป็นสีเขียว
    }
}
// เรียกใช้ฟังก์ชัน checkPasswordMatch ทุกครั้งที่พิมพ์ใน input
document.getElementById("confirmPassword").addEventListener("input", checkPasswordMatch);

// function ในการตรวจสอบความปลอดภัยของรหัสผ่าน
// function ตรวจสอบ Password
// ตรวจสอบความปลอดภัยของรหัสผ่าน
function checkPasswordStrength(password) {
    let lengthRegex = /.{8,}/; // ตรวจสอบความยาวของอย่างน้อย 8 ตัวอักษร
    let uppercaseRegex = /[A-Z]/; // ตรวจสอบตัวอักษรตัวใหญ่
    let lowercaseRegex = /[a-z]/; // ตรวจสอบตัวอักษรตัวเล็ก
    let digitRegex = /\d/; // ตรวจสอบตัวเลข
    let specialCharRegex = /[!@#$%^&*.]/; // ตรวจสอบอักขระพิเศษ

    let hasLength = lengthRegex.test(password);
    let hasUppercase = uppercaseRegex.test(password);
    let hasLowercase = lowercaseRegex.test(password);
    let hasDigit = digitRegex.test(password);
    let hasSpecialChar = specialCharRegex.test(password);

    let missingRequirements = 5 - [hasLength, hasUppercase, hasLowercase, hasDigit, hasSpecialChar].filter(Boolean).length;

    return {
        hasLength,
        hasUppercase,
        hasLowercase,
        hasDigit,
        hasSpecialChar,
        missingRequirements
    };
}

document.getElementById("Memberpassword").addEventListener("input", function() {
    let password = this.value;
    let {
        hasLength,
        hasUppercase,
        hasLowercase,
        hasDigit,
        hasSpecialChar,
        missingRequirements
    } = checkPasswordStrength(password);
    let passwordStrength = document.getElementById("passwordStrength");

    if (missingRequirements > 0) {
        passwordStrength.style.backgroundColor = "red";
        passwordStrength.innerText = `กรุณาเพิ่ม ${missingRequirements} ข้อจาก: ความยาวอย่างน้อย 8 ตัวอักษร, ตัวอักษรตัวใหญ่, ตัวอักษรตัวเล็ก, ตัวเลข, และ!@#$%^&*.`;
    } else {
        passwordStrength.style.backgroundColor = "green";
        passwordStrength.innerText = "รหัสผ่านมีความปลอดภัยและรัดกุม";
    }
});







// function ในการตรวจสอบช่องว่างของการกรอกข้อมูลสมัครสมาชิก
function validateForm() {
    let form = document.getElementById("AddMember"); // เลือกฟอร์มที่มี ID เป็น "AddMember" เท่านั้น
    let email = form.querySelector("#Memberemail").value;
    let password = form.querySelector("#Memberpassword").value;
    let confirmPassword = form.querySelector("#confirmPassword").value;
    let name = form.querySelector("#Membername").value;
    let lastname = form.querySelector("#Memberlastname").value;
    let address = form.querySelector("#Memberaddress").value;
    let phone = form.querySelector("#Memberphone").value;

    if (email === "" || password === "" || confirmPassword === "" || name === "" || lastname === "" || address === "" || phone === "") {
        alert("กรุณากรอกข้อมูลให้ครบทุกช่อง");
        return false;
    }
    return true;
}

document.getElementById("AddMember").addEventListener("submit", function(e) { // เช็คเมื่อฟอร์มที่มี ID เป็น "AddMember" ถูกส่ง
    if (!validateForm()) {
        e.preventDefault();
    }
});

// add member popup
function AddMember() {
    var popup = document.getElementById('AddMember');

    popup.style.display = 'block';
}
function Cancel() {
    var popup = document.getElementById('AddMember');

    popup.style.display = 'none';
}

