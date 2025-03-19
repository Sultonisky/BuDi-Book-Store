var tooltipTriggerList = [].slice.call(
  document.querySelectorAll('[data-bs-toggle="tooltip"]')
);
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl);
});

var message = "<?php echo isset($message) ? 'true' : 'false'; ?>";
if (message === "true") {
  var myModal = new bootstrap.Modal(document.getElementById("exampleModal"));
  myModal.show();
}

document.addEventListener("DOMContentLoaded", function () {
  // Fungsi untuk menangani tombol edit
  function setEditData(buttonClass, fieldIds) {
    document.querySelectorAll(buttonClass).forEach((button) => {
      button.addEventListener("click", function () {
        fieldIds.forEach((field) => {
          const input = document.getElementById(field.id);
          if (input) {
            input.value = this.getAttribute(field.attr);
          }
        });

        // Khusus untuk gambar preview pada edit produk
        if (buttonClass === ".edit-btn") {
          const preview = document.getElementById("edit_preview");
          if (preview) preview.src = this.getAttribute("data-image");
        }
      });
    });
  }

  // Fungsi untuk menangani tombol delete
  function setDeleteData(buttonClass, inputId) {
    document.querySelectorAll(buttonClass).forEach((button) => {
      button.addEventListener("click", function () {
        const input = document.getElementById(inputId);
        if (input) input.value = this.getAttribute("data-id");
      });
    });
  }

  // Menangani tombol edit
  setEditData(".edit-btn", [
    { id: "edit_id", attr: "data-id" },
    { id: "edit_product_name", attr: "data-name" },
    { id: "edit_price", attr: "data-price" },
  ]);

  setEditData(".edit-order-btn", [
    { id: "edit_order_id", attr: "data-id" },
    { id: "edit_status", attr: "data-status" },
  ]);

  // Menangani tombol delete
  setDeleteData(".delete-order-btn", "delete_order_id");
  setDeleteData(".delete-user-btn", "delete_user_id");
  setDeleteData(".delete-message-btn", "delete_message_id");
});

// document.addEventListener("DOMContentLoaded", function () {
//   const editId = document.getElementById("edit_id");
//   const editProductName = document.getElementById("edit_product_name");
//   const editPrice = document.getElementById("edit_price");
//   const editPreview = document.getElementById("edit_preview");

//   document.querySelectorAll(".edit-btn").forEach((button) => {
//     button.addEventListener("click", function () {
//       editId.value = this.getAttribute("data-id");
//       editProductName.value = this.getAttribute("data-name");
//       editPrice.value = this.getAttribute("data-price");
//       editPreview.src = this.getAttribute("data-image"); // Menampilkan gambar lama
//     });
//   });
// });

// document.addEventListener("DOMContentLoaded", function () {
//   const editOrderId = document.getElementById("edit_order_id");
//   const editOrderStatus = document.getElementById("edit_status");

//   document.querySelectorAll(".edit-order-btn").forEach((button) => {
//     button.addEventListener("click", function () {
//       editOrderId.value = this.getAttribute("data-id");
//       editOrderStatus.value = this.getAttribute("data-status");
//     });
//   });
// });

// document.addEventListener("DOMContentLoaded", function () {
//   const deleteOrderId = document.getElementById("delete_order_id");

//   document.querySelectorAll(".delete-order-btn").forEach((button) => {
//     button.addEventListener("click", function () {
//       deleteOrderId.value = this.getAttribute("data-id");
//     });
//   });
// });

// document.addEventListener("DOMContentLoaded", function () {
//   const deleteUserId = document.getElementById("delete_user_id");

//   document.querySelectorAll(".delete-user-btn").forEach((button) => {
//     button.addEventListener("click", function () {
//       deleteUserId.value = this.getAttribute("data-id");
//     });
//   });
// });

// document.addEventListener("DOMContentLoaded", function () {
//   const deleteMessageId = document.getElementById("delete_message_id");

//   document.querySelectorAll(".delete-message-btn").forEach((button) => {
//     button.addEventListener("click", function () {
//       deleteMessageId.value = this.getAttribute("data-id");
//     });
//   });
// });
