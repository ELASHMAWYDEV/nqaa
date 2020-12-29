// @ts-nocheck
console.log("connected to main js file");

/*-----------Required Functions BEGIN-----------*/

function show(e) {
  if (e !== null) e.style.display = "block";
}
function hide(e) {
  if (e !== null) e.style.display = "none";
}

/*-----------Required Functions END------------*/
/***********************************************/

/*---------------MAIN FUNCTIONS---------------*/

//burger menu
function burgerMenu(el) {
  let sidebar = document.querySelector(".sidebar");
  sidebar.classList.toggle("sidebar-active");
  if (sidebar.classList.contains("sidebar-active")) {
    window.addEventListener("mouseup", handler);
    function handler(e) {
      e.preventDefault();

      if (!sidebar.contains(e.target)) {
        sidebar.classList.toggle("sidebar-active");
        this.removeEventListener("mouseup", handler);
      }
    }
  }
}

/*---------closing----------------*/
function closeBox(el) {
  hide(el.parentNode);
  hide(document.querySelector(".box-container"));
}
/**********************************/

/*------select tag apperance------*/
function rotateSelect(select) {
  var span = select.nextElementSibling;
  span.classList.toggle("rotate");
  select.addEventListener("blur", handler);
  function handler(e) {
    span.classList.toggle("rotate");
    this.removeEventListener("blur", handler);
  }
}
/**********************************/

/*---------Delete user Abort--------*/
function abort(box) {
  event.preventDefault();
  hide(document.querySelector(box));
  hide(document.querySelector(".box-container"));
}
/************************************/

function popupBox(el, container = true) {
  el = document.querySelector(el);

  show(el);
  if (container) {
    //FLOATING BOX POSTION FIX
    el.style.top = window.pageYOffset + "px";

    el.parentNode.style.display = "flex";
    show(document.querySelector(".box-container"));
  }
  window.addEventListener("mouseup", handler);
  function handler(e) {
    e.preventDefault();
    if (!el.contains(e.target)) {
      if (container) {
        hide(document.querySelector(".box-container"));
      }
      hide(el);
      this.removeEventListener("mouseup", handler);
    }
  }
}
///////////////////////////////////////////

/*------------Pagination BEGIN-----------*/

function SetupPagination(page_count) {
  let table_items = document.querySelectorAll("table tbody tr");
  let pagination_container = document.querySelector(".pagination");

  // document.querySelector(".table-container").scrollIntoView();
  pagination_container.innerHTML = "";

  //Set the function
  let updateFunctionName = pagination_container.getAttribute(
    "data-update-function"
  );

  //prev button
  let prev_btn = document.createElement("div");
  prev_btn.classList.add("prev-btn");
  pagination_container.appendChild(prev_btn);

  //next button
  let next_btn = document.createElement("div");
  next_btn.classList.add("next-btn");
  pagination_container.appendChild(next_btn);

  if (page_count < 5) {
    next_btn.style.display = "none";
    prev_btn.style.display = "none";

    for (let i = 1; i < page_count + 1; i++) {
      let button = PaginationBtn(i);
      pagination_container.appendChild(button);
      button.onclick = (e) => {
        eval(`${updateFunctionName}({page: ${current_page}})`);
      }
    }
  } else {
    next_btn.style.display = "flex";

    let btn_list = [];


    for (let i = 1; i < page_count + 1; i++) {
      let button = PaginationBtn(i);

      //save all buttons in a list
      btn_list.push(button);
      btn_list.slice(4).forEach((btn) => (btn.style.display = "none"));

      pagination_container.insertBefore(button, next_btn);
      checkButtons(current_page, page_count, next_btn, prev_btn, btn_list);

      button.onclick = (e) => {
        e.preventDefault();
        if (i > 5 && current_page == 4) {
          pagination_container.insertBefore(button, next_btn);
        }
        current_page = btn_list.indexOf(button) + 1;

        checkButtons(current_page, page_count, next_btn, prev_btn, btn_list);
      };

      //next button
      next_btn.onclick = (e) => {
        if (current_page == page_count) {
          return false;
        }
        current_page += 1;

        let old_btn = btn_list.find((btn) =>
          btn.classList.contains("active-page")
        );
        old_btn.classList.remove("active-page");

        let new_btn = btn_list[btn_list.indexOf(old_btn) + 1];
        new_btn.classList.add("active-page");

        eval(`${updateFunctionName}({page: ${current_page}})`);

        checkButtons(current_page, page_count, next_btn, prev_btn, btn_list);
      };

      //prev button
      prev_btn.onclick = (e) => {
        if (current_page == 1) {
          return false;
        }

        current_page -= 1;

        let old_btn = btn_list.find((btn) =>
          btn.classList.contains("active-page")
        );
        old_btn.classList.remove("active-page");

        let new_btn = btn_list[btn_list.indexOf(old_btn) - 1];
        new_btn.classList.add("active-page");

        eval(`${updateFunctionName}({page: ${current_page}})`);

        checkButtons(current_page, page_count, next_btn, prev_btn, btn_list);
      };
    }
  }
}

function PaginationBtn(page) {
  let button = document.createElement("button");
  button.innerText = page;
  if (current_page == page) button.classList.add("active-page");
  button.addEventListener("click", function (e) {
    current_page = page;

    let current_btn = document.querySelector(".pagination button.active-page");
    current_btn.classList.remove("active-page");
    this.classList.add("active-page");
  });
  return button;
}

function checkButtons(current_page, page_count, next_btn, prev_btn, btn_list) {
  //prev button display
  if (
    (current_page == 4 && prev_btn.style.display == "none") ||
    current_page > 4
  ) {
    prev_btn.style.display = "flex";
  }

  if (current_page > 1) {
    prev_btn.style.display = "flex";
  }

  //next button display
  if (page_count == 1) {
    next_btn.style.display = "none";
  }

  /////////////////////////////////

  if (current_page >= 4 && page_count > 4) {
    btn_list
      .slice(0, current_page - 3)
      .forEach((btn) => (btn.style.display = "none"));
    btn_list
      .slice(current_page + 1)
      .forEach((btn) => (btn.style.display = "none"));
    btn_list
      .slice(current_page - 3, current_page + 1)
      .forEach((btn) => (btn.style.display = "flex"));
  } else if (current_page < 4 && page_count > 4) {
    btn_list.slice(4).forEach((btn) => (btn.style.display = "none"));
    btn_list
      .slice(0, current_page + 1)
      .forEach((btn) => (btn.style.display = "flex"));
  }

  if (current_page == page_count) {
    btn_list
      .slice(current_page - 4)
      .forEach((btn) => (btn.style.display = "flex"));
  }
}
//THIS WHOLE SECTION IS PUT IN THE END OF ANY PAGE THAT HAS A TABLE
//==>START<==//
// var table_items = document.querySelectorAll('table tbody tr');
// var table_body = document.querySelector('table tbody');
// var pagintation_container = document.querySelector('.pagination');
// var current_page = 1;
// var rows_per_page = 10; ///number of rows shown on all tables
// SetupPagination(table_items, table_body, rows_per_page, pagintation_container);
// DisplayTable(table_items, table_body, rows_per_page, current_page);
//==>END<==//

/*------------Pagination END-----------*/
/////////////////////////////////////////

function setItemsToTable() {}

/*---------Search Functions BEGIN--------------*/

function searchInput(input, child, exact = false) {
  let table_items = document.querySelectorAll(
    `.table table tr td:nth-child(${child})`
  );
  var new_table_rows = [];

  table_items.forEach((item) => {
    if (!exact) {
      if (item.textContent.toLowerCase().includes(input.value.toLowerCase())) {
        item.parentElement.style.display = "";
        new_table_rows.push(item.parentElement);
      } else {
        item.parentElement.style.display = "none";
        let index = new_table_rows.indexOf(item.parentElement);
        if (index > -1) new_table_rows.splice(index, 1);
      }
    } else {
      if (item.textContent.toLowerCase() == input.value.toLowerCase()) {
        item.parentElement.style.display = "";
        new_table_rows.push(item.parentElement);
      } else {
        item.parentElement.style.display = "none";
        let index = new_table_rows.indexOf(item.parentElement);
        if (index > -1) new_table_rows.splice(index, 1);
      }
    }
  });

  new_table_rows.forEach((row) => {
    if (new_table_rows.indexOf(row) % 2 == 1) {
      row.style.backgroundColor = "#fff";
    } else {
      row.style.backgroundColor = "#D1E1F0";
    }
  });

  SetupPagination(new_table_rows, pagintation_container);
  DisplayTable(new_table_rows, 10, 1);
  resetAll(input);
}

function resetAll(excludedItem) {
  let container = document.querySelector(".search-container");
  let inputs = container.querySelectorAll("input");
  let selects = container.querySelectorAll("select");

  //datepicker reset
  if (excludedItem != document.querySelector('input[name="date-search"]')) {
    let datepicker = document.querySelector('div[type="datepicker"]');

    datepicker.querySelector(".placeholder").innerText = "التاريخ";
    datepicker.setAttribute("value", "");
    datepicker.querySelector("input").setAttribute("value", "");
  }

  inputs.forEach((input) => {
    if (input != excludedItem) input.value = "";
  });

  selects.forEach((select) => {
    if (select != excludedItem) select.selectedIndex = 0;
  });
}

/*---------Search Functions END--------------*/

/*---------------Time Picker BEGIN-----------------*/
function timepicker(min = "9:00 am", max = "8:30 pm", night = false) {
  let timeSelects = document.querySelectorAll('select[type="timepicker"]');

  let timeList = [
    "12:00 am",
    "12:30 am",
    "1:00 am",
    "1:00 am",
    "2:00 am",
    "2:30 am",
    "3:00 am",
    "3:30 am",
    "4:00 am",
    "4:30 am",
    "5:00 am",
    "5:30 am",
    "6:00 am",
    "6:30 am",
    "7:00 am",
    "7:30 am",
    "8:00 am",
    "8:30 am",
    "9:00 am",
    "9:30 am",
    "10:00 am",
    "10:30 am",
    "11:00 am",
    "11:30 am",
    "12:00 pm",
    "12:30 pm",
    "1:00 pm",
    "1:30 pm",
    "2:00 pm",
    "2:30 pm",
    "3:00 pm",
    "3:30 pm",
    "4:00 pm",
    "4:30 pm",
    "5:00 pm",
    "5:30 pm",
    "6:00 pm",
    "6:30 pm",
    "7:00 pm",
    "7:30 pm",
    "8:00 pm",
    "8:30 pm",
    "9:00 pm",
    "9:30 pm",
    "10:00 pm",
    "10:30 pm",
    "11:00 pm",
    "11:30 pm",
  ];

  minIndex = timeList.indexOf(min);
  maxIndex = timeList.indexOf(max);

  timeSelects.forEach((timeSelect) => {
    timeSelect.style.direction = "ltr";
    let defaultOption = document.createElement("option");
    defaultOption.innerText =
      night == false ? "اختر من الدوام الصباحي" : "اختر من الدوام المسائي";
    defaultOption.setAttribute("disabled", "true");
    timeSelect.appendChild(defaultOption);

    for (let i = minIndex; i <= maxIndex; i++) {
      let option = document.createElement("option");
      option.innerText = timeList[i];
      option.innerText += night == false ? " دوام صباحي" : " دوام مسائي";
      if (night) option.setAttribute("value", timeList[i] + " → دوام مسائي");
      else option.setAttribute("value", timeList[i] + " → دوام صباحي");
      timeSelect.appendChild(option);
    }
  });
}
/*---------------Time Picker END-----------------*/
