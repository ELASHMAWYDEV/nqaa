function datepicker() {

    //get all div with type="datepicker"
    let inputs = document.querySelectorAll('div[type="datepicker"]');

    /*-------Styles BEGIN---------*/
    let styles = `
        div[type="datepicker"] {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 190px;
            height: max-content;
            font-family: Cairo;
            position: relative;
            direction: ltr;
            outline: none;
            border: none;
        }

        div[type="datepicker"] .placeholder {
            width: 100%;
            text-align: center;
            font-size: 20px;
            font-weight: 400;
            border-radius: 20px;
            box-sizing: border-box;
            outline: none;
            border: none;
            background-color: #fff;
            box-shadow: 0px 0px 5px #2c3e5057;
            cursor: pointer;
            color: #000;
            display: flex;
            justify-content: center;
            align-items: center;
            max-width: 100%;

        }

        div[type="datepicker"] .placeholder:active {
            background-color: #eee;
        }

        div[type="datepicker"] .date-container {
            display: none;
            position: absolute;
            width: 250px;
            height: max-content;
            background-color: #fff;
            top: calc(100% + 15px);
            left: 50%;
            transform: translateX(-50%);
            z-index: 2;
            border-radius: 7px;
            box-shadow: inhiret;
            cursor: default;
            box-shadow: 0px 0px 5px #2c3e5057;
            animation: dateContainer 0.5s ease 1;
            max-width: 100%;
        }

        div[type="datepicker"] .date-container::before {
            content: '';
            display: block;
            position: absolute;
            top: 0;
            left: 50%;
            width: 20px;
            height: 20px;
            transform: rotate(45deg) translateX(-50%);
            background-color: #fff;
            z-index: -1;

        }

        @keyframes dateContainer {
            0% {
                opacity: 0;
                transform: translateY(-20px) translateX(-50%);
            }
            100% {
                opacity: 1;
                transform: translateY(0px) translateX(-50%);
            }
        }

        div[type="datepicker"] .date-container .head {
            display: flex;
            flex-wrap: nowrap;
            border-bottom: 2px solid #eee;
        } 

        div[type="datepicker"] .date-container .head .month-year-indicator {
            font-size: 16px;
            margin: auto;
            cursor: default;
        } 

        div[type="datepicker"] .date-container .head .prev-month,
        div[type="datepicker"] .date-container .head .next-month  {
            font-size: 20px;
            padding: 0 12px 0 15px;
            cursor: pointer;
            
        } 

        div[type="datepicker"] .date-container .head .prev-month {
            border-radius: 7px 0 0 0;
        }
        
        div[type="datepicker"] .date-container .head .next-month {
            border-radius: 0 7px 0 0;
        }

        div[type="datepicker"] .date-container .head .prev-month:active,
        div[type="datepicker"] .date-container .head .next-month:active {
            background-color: #2c3e50;
            color: #fff;
        } 

        div[type="datepicker"] .date-container .days-container {
            width: 100%;
            height: max-content;
            border-radius: 0 0 7px 7px;
            overflow:hidden;
            display: grid;
            grid-template-columns: repeat(7, calc(100% / 7));
            font-size: 16px;
        }

        div[type="datepicker"] .date-container .days-container::-webkit-scrollbar {
            width: 0;
        }

        div[type="datepicker"] .date-container .days-container .day-box {
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            padding: 0 10px;
            transition: 0.1s ease;
        }

        div[type="datepicker"] .date-container .days-container .day-box.active-day {
            background-color: #2c3e50;
            color: #fff;
            
        }

        div[type="datepicker"] .date-container .days-container .day-box:hover {
            background-color: #2c3e50;
            color: #fff;
        }

    `;
    let styleSheet = document.createElement('style');
    styleSheet.type = "text/css";
    styleSheet.innerText = styles;
    document.head.appendChild(styleSheet);
    /*---------Styles END-----------*/



    //get date
    let now = new Date();
    let day = now.getDate();
    let month = now.getMonth() + 1;
    let year = now.getFullYear();


    let selectedDay = day;
    let selectedMonth = month;
    let selectedYear = year;

    //set to readonly & set date
    inputs.forEach(input => {

        //placeholder to display the date
        let placeholder = document.createElement('div');
        placeholder.classList.add('placeholder');
        placeholder.textContent = input.getAttribute('placeholder');
        input.appendChild(placeholder);


        //prepare the input tag & append it
        let inputTag = document.createElement('input');
        inputTag.setAttribute('type', 'hidden');
        inputTag.setAttribute('name' , input.getAttribute('name'));
        inputTag.setAttribute('id', input.getAttribute('id'));
        input.appendChild(inputTag);


        /*-------Create the container & its elements BEGIN-------*/
        //create all elements
        let container = document.createElement('div');
        let containerHead = document.createElement('div');
        let prevMonth = document.createElement('div');
        let nextMonth = document.createElement('div');
        let monthYearIndicator = document.createElement('div');
        let daysContainer = document.createElement('div');

        //set class & text and append to the container

        containerHead.classList.add('head');
        container.appendChild(containerHead);

        prevMonth.innerText = "<";
        prevMonth.classList.add('prev-month');
        containerHead.appendChild(prevMonth);

        monthYearIndicator.classList.add('month-year-indicator');
        monthYearIndicator.innerText = `${getMonth(month)} ${year}`;
        containerHead.appendChild(monthYearIndicator);

        nextMonth.innerText = ">";
        nextMonth.classList.add('next-month');
        containerHead.appendChild(nextMonth);

        daysContainer.classList.add('days-container');
        container.appendChild(daysContainer);



        container.classList.add('date-container');
        input.appendChild(container);
        /*-------Create the container & its elements END-------*/



        /*-----------Functions BEGIN-------------*/
        function changeIndicator() {
            monthYearIndicator.innerText = `${getMonth(month - 1)} ${year}`;
            displayDays();
        }

        function goToPrevMonth() {
            month--;
            
            if (month < 1) {
                month = 12;
                year--
            }
            changeIndicator()
        }

        function goToNextMonth() {
            month++;
            if (month > 12) {
                month = 1;
                year++;
            }
            changeIndicator()
        }

        function displayDays() {
            daysContainer.innerHTML = "";
            let maxDays = 31;

            //custom months
            if (month == 2) maxDays = 28;
            else if (month == 4 || month == 6 || month == 9 || month == 11) maxDays = 30;
            
            for (let day = 1; day <= maxDays; day++) {
                let dayBox = document.createElement('div');
                dayBox.classList.add('day-box');
                dayBox.innerText = day;
                daysContainer.appendChild(dayBox);
            }
            setDayBox();
        }

        function setDayBox() {
            let dayBoxs = document.querySelectorAll('.day-box');
            let dayElement = dayBoxs[day - 1];
            dayElement.classList.add('active-day'); 





            dayBoxs.forEach(dayBox => {
                dayBox.onclick = e => {
                    dayElement.classList.remove('active-day');
                    dayElement = dayBox;
                    dayElement.classList.add('active-day');
                    selectedDay = Array.from(dayBoxs).indexOf(dayBox) + 1;

                    day = selectedDay;
                    selectedMonth = month;
                    selectedYear = year;

                    //set the placeholder and the input value
                    
                    input.setAttribute('value', selectedDate());
                    placeholder.textContent = selectedDate();
                    inputTag.setAttribute('value', selectedDate());

                    container.style.display = "none";

                }
            });


            if (selectedDay == day && selectedMonth == month && selectedYear == year) {
                dayElement.classList.add('active-day');
            } else {
                dayElement.classList.remove('active-day');
            }



        }


        /*-----------Functions END-------------*/



        placeholder.onclick = e => {
            e.preventDefault();

            container.style.display = 'block';
            //create all needed elements
            displayDays();
            
            
            //prev & next
            prevMonth.onclick = e => goToPrevMonth();
            nextMonth.onclick = e => goToNextMonth();



            //set the placeholder and the input value
            
            
            input.setAttribute('value', selectedDate());
            placeholder.textContent = selectedDate();
            inputTag.setAttribute('value', selectedDate());



        }

        //if clicked outside
        window.addEventListener('mouseup', handler);

        function handler(e) {

            let container = document.querySelector('div[type="datepicker"] .date-container');
            let placeholder = document.querySelector('div[type="datepicker"] .placeholder');

            if (!container.contains(e.target) && !placeholder.contains(e.target)) {
                container.style.display = 'none';
            }
        }
    });
    
    



    function selectedDate() {
        let selectedDate = (selectedDay < 10) ? `0${selectedDay}` : selectedDay;
        selectedDate += (selectedMonth < 10) ? `/0${selectedMonth}` : `/${selectedMonth}`;
        selectedDate += `/${selectedYear}`;
        return selectedDate;
    }

    function getMonth(month) {
        let monthList = ['يناير', 'فبراير', 'مارس', 'ابريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
        return monthList[month];
    }


}
datepicker();