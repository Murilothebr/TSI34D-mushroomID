document.addEventListener("DOMContentLoaded", function () {
  function imagePreview() {
    const imagePreviewInput = document.getElementById("image_preview_input");
    const preview = document.getElementById("image_preview");
    const imagePreviewSubmit = document.getElementById("image_preview_submit");

    if (!(imagePreviewInput && preview)) return;

    imagePreviewInput.style.display = "none";
    imagePreviewSubmit.style.display = "none";

    preview.addEventListener("click", function () {
      imagePreviewInput.click();
    });

    imagePreviewInput.addEventListener("change", function (event) {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          preview.src = e.target.result;
          imagePreviewSubmit.style.display = "block";
        };
        reader.readAsDataURL(file);
      }
    });
  }

  function hideFlashMessage() {
    setTimeout(function () {
      const flashMessage = document.getElementById("flash-message");
      if (flashMessage) {
        const alert = bootstrap.Alert.getOrCreateInstance(flashMessage);
        alert.close();
      }
    }, 1500);
  }

  hideFlashMessage();
  imagePreview();
});
