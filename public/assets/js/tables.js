// @ts-nocheck
/*-------------------AJAX Table START-------------------*/

function updateOrdersTable({ page = 1 }) {
  //Get all data for search
  ajax("post", ajaxUrl + "orders", { get_orders: true, page }, (output) => {
    output = JSON.parse(output);

    page_count = output.page_count;

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

    tbody.innerHTML = tbodyContent;
    SetupPagination(page_count);
  });
}
/*-------------------AJAX Table END---------------------*/
