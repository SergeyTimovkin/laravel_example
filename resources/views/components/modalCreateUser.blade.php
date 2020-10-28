<button
        type="button"
        class="btn btn-primary"
        data-toggle="modal"
        data-target="#createUserModal"
>
    Создать пользователя
</button>
<div
        id="createUserModal"
        class="modal fade bd-example-modal-lg"
        tabindex="-1"
        role="dialog"
        aria-labelledby="myLargeModalLabel"
        aria-hidden="true"
>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">
                    Создание нового пользователя
                </div>
            </div>
            <div class="modal-body">
                <form id="userform">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="username">
                            Username*
                        </label>
                        <input
                                id="username"
                                type="text"
                                name="username"
                                class="form-control"
                        >
                    </div>
                    <div class="form-group">
                        <label for="phone">
                            Номер телефона*
                        </label>
                        <input
                                id="phone"
                                type="text"
                                name="phone"
                                class="form-control"
                        >
                    </div>
                    <div class="form-group">
                        <label for="age">
                            Возраст*
                        </label>
                        <input
                                id="age"
                                type="number"
                                name="age"
                                min="1"
                                max="99"
                                class="form-control"
                        >
                    </div>
                    <div class="form-group">
                        <input
                                id="male"
                                name="gender"
                                type="radio"
                                value="1"
                                checked
                        >
                        <label for="male">Муж.</label>
                        <input
                                type="radio"
                                id="female"
                                name="gender"
                                value="0"
                        >
                        <label for="female">Жен.</label>
                    </div>
                    <div class="form-group">
                        <select
                                id="region_id"
                                name="region_id"
                                class="custom-select"
                        >
                            @foreach($regions as $region)
                                <option value="{{$region->id}}">{{$region->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button
                        id="submitFormBtn"
                        type="button"
                        class="btn btn-primary">
                    Создать
                </button>
                <button
                        type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal"
                >
                    Закрыть
                </button>
            </div>
        </div>
    </div>
</div>
<script
        src="{{ asset('js/jquery.maskedinput.js') }}"
></script>
<script>
    $('document').ready(function () {
        const $phone = $("#phone");
        const $age = $("#age");
        const $username = $("#username");
        const $userForm = $("#userform");
        const $submitFormBtn = $("#submitFormBtn");

        $phone.mask("79999999999");

        $submitFormBtn.click(function () {
            let errors = 0;
            if (!$phone.val()) {
                alert('Не заполнен номер телефона')
                errors++;
            }
            if (!$username.val()) {
                alert('Не заполнен username')
                errors++;
            }
            if (!$age.val()) {
                alert('Не заполнен возраст')
                errors++;
            }
            if (!errors) {
                $.ajax({
                    url: '{{url('api/main/addUser')}}',
                    method: 'post',
                    dataType: 'json',
                    data: {
                        data: $userForm.serialize()
                    },
                    success() {
                        alert('Пользователь успешно добавлен');
                        location.reload();
                    },
                    error(error) {
                        alert(error.responseJSON.message);
                    }
                });
            }
        });
    });
</script>
