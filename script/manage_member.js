// ฟังก์ชันเพื่อกำหนดรูปแบบเบอร์โทรศัพท์
function formatPhoneNumber(input) {
    input.value = input.value.replace(/\D/g, '');
    if (input.value.length === 10) {
        let formattedPhoneNumber = input.value.slice(0, 3) + '-' + input.value.slice(3, 6) + '-' + input.value.slice(6, 10);
        input.value = formattedPhoneNumber;
    }
}

// Function to check password match
function checkPasswordMatch() {
    let password = document.getElementById("Memberpassword").value;
    let confirmPassword = document.getElementById("confirmPassword").value;
    let errorDiv = document.getElementById("passwordError");

    if (password !== confirmPassword) {
        errorDiv.innerHTML = "รหัสผ่านไม่ตรงกัน";
        errorDiv.style.color = "red";
    } else {
        errorDiv.innerHTML = "รหัสผ่านตรงกัน";
        errorDiv.style.color = "green";
    }
}
document.getElementById("confirmPassword").addEventListener("input", checkPasswordMatch);

// Function to check password strength
function checkPasswordStrength(password) {
    let lengthRegex = /.{8,}/;
    let uppercaseRegex = /[A-Z]/;
    let lowercaseRegex = /[a-z]/;
    let digitRegex = /\d/;
    let specialCharRegex = /[!@#$%^&*.]/;

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

// Function to validate form
function validateForm() {
    let form = document.getElementById("AddMemberForm");
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

document.getElementById("AddMemberForm").addEventListener("submit", function(e) {
    if (!validateForm()) {
        e.preventDefault();
    }
});
