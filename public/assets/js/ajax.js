console.log("connected to ajax file");


/*-----------Required Functions BEGIN-----------*/
function ajax(type, url, data, success)
{
    type = type.toUpperCase(); //transform type to uppercase to avoid errors

    //string the submitted data
    params = '?';
    for(var i in data) {
        params += i + '=' + data[i] + '&';
    }
    params = params.slice(0, -1);


    var xhr = new XMLHttpRequest();

    //get request
    if(type == 'GET')
    {
        url += params; //append data to url
        xhr.open(type, url, true);
        xhr.onloadend = function()
        {
            if(this.status == 200)
            {
                success(this.responseText);

            }
        }
        xhr.send();

    }
    else if(type == 'POST')
    {
        // remove the ? from @params
        params = params.slice(1);
        xhr.open(type, url, true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.onloadend = function()
        {
            if(this.status == 200)
            {
                success(this.responseText);

            }
        }
        xhr.send(params);
    }
    else
    {
        console.log("Developer Error => the request type: "+type+" is invalid");
    }
}


//convert all inputs to a json object
function inputToJson(parent,inputs)
{
    data = {
        success : [],
        errors : [],
        output : {}
    };

    for(let input in inputs) {
        data.output[inputs[input]] = parent.querySelector('input[name="'+ inputs[input] +'"]').value;
    }
    return data;
}



//put the returned values to the input fields
function outputToInput(parent,outputs)
{
    for(var output in outputs) {
        parent.querySelector('input[name="'+ output +'"]').setAttribute('value', outputs[output]);

    }
}


//display messages function => on ajax success => messages(ouput.errors, output.success);
function messages(errors = [], success = [])
{
    alarms = document.getElementById('alarms');
    if(errors.length > 0 || success.length > 0)
    {   
        show(alarms);

        //POSITION FIX
        alarms.style.top = window.pageYOffset + 20 + 'px';
        window.addEventListener('scroll', (e) => {
            alarms.style.top = window.pageYOffset + 20 + 'px';
        });

        var alarm = '';
        errors.forEach(err => {
            alarm += '<div class="alarm alarm-errors">' + err + '</div>';
        });

        success.forEach(succ => {
            alarm += '<div class="alarm alarm-success">' + succ + '</div>';
        })
        alarm += '</div>';
        alarms.innerHTML += alarm;
        setTimeout(function(){
            [].forEach.call(document.querySelectorAll('.alarm'), function(el) {
                el.remove();
            });
            hide(alarms);
        }, 3000);

    }
    else
    {
        return false;
    }
    
}

/*-----------Required Functions END------------*/
/***********************************************/
//save personal account settings

function update_account_settings(form){
    event.preventDefault();
    var update_account_settings = 'update_account_settings';
    data = inputToJson(form, ['username', 'email', 'name', 'phone']);
    data = JSON.stringify(data);
    data = {update_account_settings:update_account_settings, data:data};
    ajax('post', ajaxUrl + 'common', data, function(output){
        output = JSON.parse(output);
        messages(output.errors, output.success);
        if(output.reload == 'true')
        {
            setTimeout(function(){
                window.location.reload();
            },2000);
        }
        else
        {
            outputToInput(form ,output.output);
        }            
    });
}
    


// //change personal account passwrod

function change_account_password(form){
    event.preventDefault();
    var change_account_password = 'change_account_password';
    data = inputToJson(form, ['pass1', 'pass2']);

    data = JSON.stringify(data);
    data = {change_account_password:change_account_password, data:data};
    ajax('post', ajaxUrl + 'common', data, function(output){
        output = JSON.parse(output);
        messages(output.errors, output.success);
    });
}
    



// //add new user
// function add_new_user(form) {
//     event.preventDefault();
//     var add_new_user = 'add_new_user';
//     data = inputToJson(form, {
//         username:'input',
//         email:'input',
//         name:'input',
//         phone:'input',
//         pass1:'input',
//         pass2:'input',
//         lvl: 'select'
//     });

//     data = JSON.stringify(data);
//     data = {add_new_user:add_new_user, data:data};
//     ajax('post', ajaxUrl + 'users', data, function(output){
//         output = JSON.parse(output);
//         messages(output.errors, output.success);
//         if(output.reload == 'true')
//         {
//             setTimeout(function(){
//                 window.location.reload();
//             },2000);
//         }
//         else
//         {
//             outputToInput(form ,output.output);
//         }            
//     });
// }

// /*----------Edit user---------*/
function get_user_edit(el) {
    event.preventDefault();
    id = el.getAttribute('data-user-id');
    var edit_user_box = document.querySelector('.edit-user-box');
    edit_user_box.querySelector('span.user_id').textContent = id;
    edit_user_box.querySelectorAll('input[name="id"]').forEach(input => input.setAttribute('value', id));
    //get data
    var get_user_by_id = 'get_user_by_id';
    data = {get_user_by_id:get_user_by_id, id:id};
    ajax('post', ajaxUrl + 'users', data, function(output){
        output = JSON.parse(output);
        messages(output.errors, output.success);
        if(output.reload == 'true')
        {
            setTimeout(function(){
                window.location.reload();
            },1000);
        }
        else
        {
            outputToInput(edit_user_box, output.output); 
            edit_user_box.querySelector('option[value="'+ output.lvl +'"]').setAttribute('selected', 'true');
            popupBox('.edit-user-box');  

        }
        
          
    });

}
    

function edit_user(form) {
    event.preventDefault();
    var edit_user = 'edit_user';
    data = inputToJson(form, ['username', 'email', 'phone', 'name']);
    data.lvl = form.querySelector('select[name="lvl"]').value;
    data['id'] = id;
    data = JSON.stringify(data);
    data = {edit_user:edit_user, data:data};
    ajax('post', ajaxUrl + 'users', data, function(output){
        output = JSON.parse(output);
        messages(output.errors, output.success);
        if(output.reload == 'true')
        {
            setTimeout(function(){
                window.location.reload();
            },2000);
        }
        else
        {
            
            outputToInput(form, output.output);
            form.querySelector('select[name="lvl"]').setAttribute('value', output.lvl);

        }
           
    });   
}

// /************************************/
// /*----------Delete user---------*/
function get_user_delete(el){
    id = el.getAttribute('data-user-id');
    var delete_user_box = document.querySelector('.delete-user-box');
    delete_user_box.querySelector('span.user_id').innerHTML = id;
}
function delete_user(form) {
    event.preventDefault();
    //get data
    var delete_user_by_id = 'get_user_by_id';
    data = {delete_user_by_id:delete_user_by_id, id:id}
    ajax('post', ajaxUrl + 'users', data, function(output){
        output = JSON.parse(output);
        messages(output.errors, output.success);
        if(output.reload == 'true')
        {
            setTimeout(function(){
                window.location.reload();
            },2000);
        }
        
           
    });   
}
    




/*------Delete News Box---------*/
function get_news_delete(el){
    id = el.getAttribute('data-news-id');
    var delete_news_box = document.querySelector('.delete-news-box');
    delete_news_box.querySelector('span.news_id').innerHTML = id;
    document.forms['delete_news_form']['id'].setAttribute('value', id);

}


function get_payment_delete(el){
    id = el.getAttribute('data-payment-id');
    let delete_payment_box = document.querySelector('.delete-payment-box');
    delete_payment_box.querySelector('span.payment_id').innerHTML = id;
    delete_payment_box.querySelector('input[name="id"]').setAttribute('value', id);
}

function get_order_edit(el) {
    id = el.getAttribute('data-order-id');
    var edit_order_box = document.querySelector('.edit-order-box');
    edit_order_box.querySelector('span.order_id').textContent = id;
    edit_order_box.querySelector('input[name="id"]').setAttribute('value', id);
    //get data
    var get_order_by_id = 'get_order_by_id';
    data = {get_order_by_id:get_order_by_id, id:id};
    ajax('post', ajaxUrl + 'orders', data, function(output){
        output = JSON.parse(output);
        messages(output.errors, output.success);
        if(output.reload == 'true')
        {
            setTimeout(function(){
                window.location.reload();
            },1000);
        }
        else
        {
            outputToInput(edit_order_box, output.output); 
            if(output.type)
                edit_order_box.querySelector('select[name="type"] option[value="'+ output.type +'"]').setAttribute('selected', 'true');

            if (output.time)
                edit_order_box.querySelector('select[name="time"] option[value="'+ output.time +'"]').setAttribute('selected', 'true');
            edit_order_box.querySelector('select[name="region"] option[value="'+ output.region +'"]').setAttribute('selected', 'true');

            if (output.status)
                edit_order_box.querySelector('select[name="status"] option[value="'+ output.status +'"]').setAttribute('selected', 'true');

            if (output.appointment_status)
                edit_order_box.querySelector('select[name="appointment_status"] option[value="'+ output.appointment_status +'"]').setAttribute('selected', 'true');

            if (output.technical)
                edit_order_box.querySelector('select[name="technical"] option[value="'+ output.technical +'"]').setAttribute('selected', 'true');
            edit_order_box.querySelector('textarea[name="details"]').innerText = output.details;

            //date
            edit_order_box.querySelector('input[name="date"]').setAttribute('value', output.date);
            edit_order_box.querySelector('div[type="datepicker"] .placeholder').innerText = output.date;
            edit_order_box.querySelector('div[type="datepicker"]').setAttribute('placeholder', output.date);
            
            popupBox('.edit-order-box');  

        }
        
          
    });
}



function get_region_edit(el) {
    let id = el.getAttribute('data-region-id');
    let edit_region_box = document.querySelector('.edit-region-box');
    edit_region_box.querySelector('span.region_id').innerHTML = id;
    edit_region_box.querySelector('input[name="id"]').setAttribute('value', id);
    const data = {get_region_by_id : 'get_region_by_id', id : id};
    console.log(id);
    ajax('post', ajaxUrl + 'regions', data, (output) => {
        output = JSON.parse(output);
        messages(output.errors, output.success);
        console.log(output);
        if(output.reload == 'true')
        {
            setTimeout(function(){
                window.location.reload();
            },1000);
        }
        else
        {
            outputToInput(edit_region_box, output.output); 
            popupBox('.edit-region-box');  
        }
    });
}