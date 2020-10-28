<button
        type="button"
        class="btn btn-success"
        data-toggle="modal"
        data-target="#importUsersExcelModal"
>
    Загрузить пользователей из Excel
</button>
<div
        id="importUsersExcelModal"
        class="modal bd-example-modal-lg"
        tabindex="-1"
        role="dialog"
        aria-labelledby="myLargeModalLabel"
        aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">
                    Загрузка пользователей из Excel
                </div>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="text-center mb-5">
                        <div id="btn_download-sample-excel-file" class="btn btn-outline-primary">
                            Скачать образец файла
                        </div>
                    </div>
                    <form class="modal-users-import-excel__form" enctype="multipart/form-data">
                        {{csrf_field()}}
                        <div class="custom-file">
                            <input type="file" id="excelFile" accept=".xls, .xlsx">
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button
                                id="submitExcelFormBtn"
                                type="button"
                                class="btn btn-primary">
                            Загрузить
                        </button>
                        <button
                                type="button"
                                class="btn btn-secondary"
                                data-dismiss="modal"
                        >
                            Закрыть
                        </button>
                    </div>
                    <div class="row">
                        <div class="col">
                            <b>Справочник ID регионов</b>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Регион</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($regions as $region)
                                    <tr>

                                        <td>{{ $region->id }}</td>
                                        <td>{{ $region->name }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('document').ready(function () {

        const $exampleFileExcelBtn = $("#btn_download-sample-excel-file");
        const $submitExcelFormBtn = $("#submitExcelFormBtn");

        $exampleFileExcelBtn.click(() => {
            location.href = '{{url ('api/main/exampleExcelFile')}}';
        });

        $submitExcelFormBtn.click(() => {
            const $excelFile = $('#excelFile');
            const formData = new FormData();
            formData.append('excel', $excelFile.prop('files')[0]);
            $.ajax({
                method: 'POST',
                url: '{{url('api/main/importUsersFromExcel')}}',
                data: formData,
                dataType: 'json',
                cache: false,
                processData: false,
                contentType: false,
                success() {
                    alert('Пользователи успешно добавлены');
                    location.reload();
                },
                error(Error) {
                    alert(Error.responseJSON.message);
                },
            });
        });
    });
</script>
