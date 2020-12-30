// @ts-nocheck
/*-------------------AJAX Table START-------------------*/

function updateOrdersTable({ page = 1 }) {
  //Get all data for search
  ajax("post", ajaxUrl + "orders", { get_orders: true, page }, (output) => {
    output = JSON.parse(output);
    messages(output.errors, output.success);
    let tbody = document.querySelector(".table-container table tbody");
    let tbodyContent = "";
    if (output.orders.length != 0)
      for (let order of output.orders) {
        tbodyContent += `
            <tr>
            <td>${order.id}</td>
            <td>${order.name}</td>
            <td>${order.phone}</td>
            <td>${order.type}</td>
            <td>${order.region}</td>
            <td>${order.address}</td>
            <td>
                ${
                  order.map_lng && order.map_lat
                    ? `
                <button class="maps-btn" onclick="show_map_box(this);showMap();" data-address="${order.address}" data-name="${order.name}" data-lng="${order.map_lng}" data-lat="${order.map_lat}" data-phone="${order.phone}">عرض الخريطة</button>
                `
                    : "لا يوجد"
                }
            </td>
            <td>${order.date}</td>
            <td dir="ltr">${order.time}</td>
            <td>${order.notes}</td>
            <td>${order.created_by}</td>
            <td>${order.create_date}</td>
            <td>${order.appointment_status}</td>
            <td>${order.money}</td>
            <td>${order.technical}</td>
            <td>${order.details}</td>
            <td>${order.status}</td>
            <td>${order.invoice_num || ""}</td>
            <td class="action">
                <button id="btn-edit" class="btn-edit" data-order-id="${
                  order.id
                }" onclick="get_order_edit(this);readOnly()">تعديل</button>
            ${
              output.role == "مدير" &&
              `
              <form method="POST" name="delete_order_form" onsubmit="return confirm('هل أنت متأكد أنك تريد حذف الطلب ؟\nلن يمكنك الرجوع عن هذا القرار !');">
              <input type="hidden" name="id" value="${order.id}">
              <button type="submit" name="delete_order" style="border: none;background: transparent;outline: none;">
              <img src="${window.location.origin}/public/assets/img/trash.svg" alt="حذف الطلب" title="حذف الطلب" class="delete_order_btn">
              </button>
              </form>`
            }
            </td>

            ${
              output.role != "فني"
                ? `
            <td>
            <a class="whatsapp-btn" href="https://api.whatsapp.com/send?phone=+966${
              order.phone
            }&text=مرحبا استاذ ${order.name}%0D%0Aشكرا لتقديمك طلب ${
                    order.type
                  } من خلال موقع شركة نقاء%0D%0Aهذه هي بياناتك...%0D%0Aالاسم: ${
                    order.name
                  }%0D%0Aالحي/المنطقة: ${order.region}%0D%0Aالعنوان: ${
                    order.address
                  }%0D%0Aالموعد: ${
                    order.date
                  } . ' ' . $order->time}%0D%0Aسوف يتواصل معك أحد موظفينا قريبا" target="_blank">تنبيه العميل</a>
            ${
              order.technical
                ? `
            <a class="whatsapp-techy-btn" href="https://api.whatsapp.com/send?phone=+966${order.technical_phone}&text=مرحبا ${order.technical}%0D%0Aهناك طلب ${order.type} جديد اضافه لك <?= $_SESSION['name']}%0D%0Aوهذه هي البيانات%0D%0Aاسم العميل: ${order.name}%0D%0Aالجوال: ${order.phone}%0D%0Aنوع الطلب: ${order.type}%0D%0Aالحي: ${order.region}%0D%0Aالعنوان: ${order.address}%0D%0Aالتاريخ: ${order.date}%0D%0Aالوقت: ${order.time}%0D%0Aملاحظات العميل: ${order.notes}" target="_blank">ارسال البيانات الي الفني</a>
            `
                : ""
            }
            </td>
            `
                : ""
            }

            </tr>
        `;
      }

    page_count = output.page_count;
    tbody.innerHTML = tbodyContent;
    SetupPagination(page_count);
  });
}

function updateRegularTable({ page = 1 }) {
  //Get all data for search
  ajax("post", ajaxUrl + "regular", { get_regular: true, page }, (output) => {
    output = JSON.parse(output);
    console.log(output);
    messages(output.errors, output.success);
    let tbody = document.querySelector(".table-container table tbody");
    let tbodyContent = "";
    if (output.regular.length != 0)
      for (let order of output.regular) {
        tbodyContent += `
        <tr>
        <td>${order.id}</td>
        <td>${order.name}</td>
        <td>${order.phone}</td>
        <td>${order.region}</td>
        <td>${order.address}</td>
        <td>${order.next_date}</td>
        <td>${order.old_order_id}</td>
        <td>${order.type}</td>
        <td>${order.status}</td>
        <td class="action">
            ${
              !order.action
                ? `
                <form method="POST" onsubmit="return confirm('هل أنت متأكد أنك أنشأت طلب جديد باسم العميل: ${order.name} \n في صفحة الطلبات ؟');">
                    <input type="hidden" name="id" value="${order.id}">
                    <button class="btn-done" type="submit">تم انشاء طلب جديد للعميل</button>
                </form>`
                : order.action
            }
            <img onclick="popupBox('.delete-regular-box'); get_regular_delete(this);" src="${
              window.location.origin
            }/public/assets/img/trash.svg" alt="حذف الطلب" title="حذف الطلب" data-regular-id="${
          order.id
        }" class="delete_regular_btn">
        </td>
        <td><a class="whatsapp-btn" href="https://api.whatsapp.com/send?phone=+966${
          order.phone
        }&text=مرحبا ${
          order.name
        }%0D%0Aلقد حان موعد الصيانة الدورية %0D%0Aيرجي تعبئة نموذج الصيانة من هنا%0D%0A<?= ROOT_URL . 'form'}%0D%0Aأو الاتصال بنا علي 05429045700542904570وشكرا جزيلا" target="_blank">تنبيه العميل</a></td>

    </tr>
        `;
      }

    page_count = output.page_count;
    tbody.innerHTML = tbodyContent;
    SetupPagination(page_count);
  });
}

function updateNotesTable({ page = 1 }) {
  //Get all data for search
  ajax("post", ajaxUrl + "notes", { get_notes: true, page }, (output) => {
    output = JSON.parse(output);
    messages(output.errors, output.success);
    let tbody = document.querySelector(".table-container table tbody");
    let tbodyContent = "";
    if (output.notes.length != 0)
      for (let note of output.notes) {
        tbodyContent += `
        <tr>
        <td>${note.id}</td>
        <td colspan="3">${note.note}</td>
        <td>${note.note_taker_name}</td>
        <td>${note.create_date}</td>

        ${
          !note.status
            ? output.role == "مدير"
              ? `
          <td class="action">
                    <form method="POST">
                        <input type="hidden" name="note_id" value="${note.id}">
                        <button class="btn-done" type="submit" name="update_note">تم الاطلاع</button>
                    </form>
                    <form method="POST">
                        <input type="hidden" name="note_id" value="${note.id}">
                        <img onclick="popupBox('.delete-note-box'); get_note_delete(this);" src="${window.location.origin}/public/assets/img/trash.svg" alt="حذف المستخدم" title="حذف المستخدم" data-note-id="${note.id}" class="delete_note_btn">
                    </form>
                    <button id="btn-edit" class="btn-edit" data-note-id="${note.id}" onclick="get_note_edit(this);">تعديل</button>

                </td>`
              : ""
            : `
                <td>${note.status}</td>`
        }

    </tr>
        `;
      }

    page_count = output.page_count;
    tbody.innerHTML = tbodyContent;
    SetupPagination(page_count);
  });
}

function updatePaymentsTable({ page = 1 }) {
  //Get all data for search
  ajax("post", ajaxUrl + "payments", { get_payments: true, page }, (output) => {
    output = JSON.parse(output);
    messages(output.errors, output.success);
    let tbody = document.querySelector(".table-container table tbody");
    let tbodyContent = "";
    if (output.payments.length != 0)
      for (let payment of output.payments) {
        tbodyContent += `
        <tr>
            <td>${payment.id}</td>
            <td>${payment.create_date}</td>
            <td>${payment.cash}</td>
            <td>${payment.net}</td>
            <td>${payment.payments}</td>
            <td>${payment.advance}</td>
            <td>${payment.total}</td>
            ${
              output.role == "مدير"
                ? `
            <td>
                    <img onclick="popupBox('.delete-payment-box'); get_payment_delete(this);" src="${window.location.origin}/public/assets/img/trash.svg" alt="حذف المبلغ اليومي" title="حذف المستخدم" data-payment-id="${payment.id}">
                    <button id="btn-edit" class="btn-edit" data-payment-id="${payment.id}" onclick="get_payment_edit(this);">تعديل</button>

                </td>`
                : ""
            }
        </tr>
        `;
      }

    page_count = output.page_count;
    tbody.innerHTML = tbodyContent;
    SetupPagination(page_count);
  });
}
/*-------------------AJAX Table END---------------------*/
