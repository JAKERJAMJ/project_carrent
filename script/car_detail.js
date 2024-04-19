//
// Popup Edit Detail Car

//
//Popup upload Picture
function showAddPicture() {
    // ดึงองค์ประกอบของป็อปอัพมา
    var popup = document.getElementById("add_picture");
    // แสดงป็อปอัพ
    popup.style.display = "block";
}

function hideAddPicture() {
    // ดึงองค์ประกอบของป็อปอัพ
    var popup = document.getElementById("add_picture");
    // ซ่อนป็อปอัพ
    popup.style.display = "none";
}

// การดูรูปภาพ
function EditImg1(event) {
  const preview = document.getElementById('preview-edit-img1');
  const file = event.target.files[0];
  
  if (file) {
      const reader = new FileReader();
      
      reader.onload = function(e) {
          preview.src = e.target.result;
      }
      reader.readAsDataURL(file);
  } else {
      preview.src = "";
  }
}

function EditImg2(event) {
  const preview = document.getElementById('preview-edit-img2');
  const file = event.target.files[0];
  
  if (file) {
      const reader = new FileReader();
      
      reader.onload = function(e) {
          preview.src = e.target.result;
      }
      reader.readAsDataURL(file);
  } else {
      preview.src = "";
  }
}

function EditImg3(event) {
  const preview = document.getElementById('preview-edit-img3');
  const file = event.target.files[0];
  
  if (file) {
      const reader = new FileReader();
      
      reader.onload = function(e) {
          preview.src = e.target.result;
      }
      reader.readAsDataURL(file);
  } else {
      preview.src = "";
  }
}
//
//
// Popup Update picture more
// แสดง popup สำหรับอัพเดตรูปภาพ
function showUpdate() {
    var popup = document.getElementById("popUpdate");
    popup.style.display = "block";
}

// ซ่อน popup สำหรับอัพเดตรูปภาพ
function hideUpdate() {
    var popup = document.getElementById("popUpdate");
    popup.style.display = "none";
}

function EditCar() {
    var popup = document.getElementById("UpdateCar");
    popup.style.display = "block";
}
function hideEditcar(){
    var popup = document.getElementById("UpdateCar");
    popup.style.display = "none";
}
