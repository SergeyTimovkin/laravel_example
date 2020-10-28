<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Phone;
use App\Models\Region;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MainController extends Controller
{
    private $request;

    /**
     * MainController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Получение всех пользователей
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function getData()
    {
        return view('main')->with(
            [
                'users' =>
                    User::query()
                        ->select()
                        ->with(['phone', 'region'])
                        ->orderBy('id', 'desc')
                        ->get(),
                'regions' => Region::query()
                    ->select()
                    ->get()
            ]
        );
    }

    /**
     * Добавление пользователя и номера телефона
     *
     * @param Request $request
     * @param array $data
     * @return string
     * @throws \Exception
     */
    public function addUser($data = [])
    {
        try {
            if (empty($data)) {
                parse_str($this->request->request->get('data'), $data);
            }
            if (User::query()->where('username', $data['username'])->exists()) {
                throw new \Exception('Username пользователя уже существует');
            }
            if (Phone::query()->where('number', $data['phone'])->exists()) {
                throw new \Exception('Номер телефона занят');
            }

            $user_id = User::query()
                ->insertGetId(
                    [
                        'username' => $data['username'],
                        'age' => array_key_exists('age', $data) ? $data['age'] : '',
                        'gender' => array_key_exists('gender', $data) ? $data['gender'] : null,
                        'region_id' => array_key_exists('region_id', $data) ? $data['region_id'] : '',
                    ]
                );
            if ($user_id) {
                Phone::query()
                    ->insert(
                        [
                            'number' => $data['phone'],
                            'user_id' => $user_id
                        ]
                    );
            }
            return [
                'message' => 'Пользователь успешно создан'
            ];
        } catch (\Exception $e) {
            throw new \Exception('Во время создания пользователя произошла ошибка. ' . $e->getMessage());
        }
    }

    /**
     * Импорт пользователей из Excel
     *
     * @param Request $request
     * @return string[]
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     *
     */
    public function importUsersFromExcel(Request $request)
    {
        $file = $request->files->get('excel');
        $file_name = $file->getClientOriginalName();
        $file_extensions = pathinfo($file_name)['extension'];
        $allowed_extensions = ['xls', 'xlsx'];

        //проверяем разрешение файла
        if (!in_array(strtolower($file_extensions), $allowed_extensions)) {
            throw new \Exception(
                'Файл имеет недопустимое расширение! Поддерживаемые расширения ' . implode(', ', $allowed_extensions)
            );
        }

        //переводим excel файл в массив
        $file_temp_path = $file->getPathname();
        $spreadsheet = IOFactory::load($file_temp_path);
        $worksheet = $spreadsheet->getActiveSheet();
        $max_row = $worksheet->getHighestDataRow();

        $raw_users = $worksheet->rangeToArray("A2:E$max_row", null, true, true, true);

        //todo добавить транзакции, чекать регулярками поля и выкидывать экзепшены
        foreach ($raw_users as $user) {
            if ($user['A'] !== null) {
                $this->addUser(
                    [
                        'username' => $user['A'],
                        'gender' => $user['B'] === 'Мужской' ? 1 : 0,
                        'age' => (int)$user['C'],
                        'region_id' => $user['D'],
                        'phone' => $user['E']
                    ]
                );
            } else {
                return [
                    'message' => 'Пользователи успешно добавлены'
                ];
            }
        }
    }

    /**
     * Образец Excel файла
     *
     * @param Spreadsheet $spreadsheet
     */
    public function exampleExcelFile(Spreadsheet $spreadsheet)
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Пример пользователей ');

        $sheet->getStyle('A1:E1')
            ->applyFromArray(
                [
                    'font' => [
                        'bold' => true,
                    ]
                ]
            );

        // ширина столбцов
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(16);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(22);
        $sheet->getColumnDimension('E')->setWidth(22);


        // правила валидации региона
        $validation = $sheet->getCellByColumnAndRow(4, 2)
            ->getDataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $validation->setAllowBlank(false);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setErrorTitle('Ошибка ввода');
        $validation->setError('Значение не из списка.');
        $validation->setPromptTitle('Выберите из списка');
        $validation->setPrompt('Пожалуйста, выберите значение из выпадающего списка.');

        // доступные регионы для валидации
        $regions = Region::query()
            ->select(['id'])
            ->get()
            ->pluck('id');


        $regions_str = '"';
        foreach ($regions as $region) {
            $regions_str .= $region . ',';
        }
        $regions_str = substr($regions_str, 0, -1);
        $regions_str .= '"';
        $validation->setFormula1($regions_str);

        // добавляем валидацию 200 ячейкам
        $number_of_cells_for_validation = 200;
        for ($i = 3; $i < $number_of_cells_for_validation; $i++) {
            $sheet->getCellByColumnAndRow(4, $i)->setDataValidation(clone $validation);
        }


        // правила валидации пола
        $validation = $sheet->getCellByColumnAndRow(2, 2)
            ->getDataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $validation->setAllowBlank(false);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setErrorTitle('Ошибка ввода');
        $validation->setError('Значение не из списка.');
        $validation->setPromptTitle('Выберите из списка');
        $validation->setPrompt('Пожалуйста, выберите значение из выпадающего списка.');
        $validation->setFormula1('"Мужской, Женский"');

        // добавляем валидацию 200 ячейкам
        $number_of_cells_for_validation = 200;
        for ($i = 3; $i < $number_of_cells_for_validation; $i++) {
            $sheet->getCellByColumnAndRow(2, $i)->setDataValidation(clone $validation);
        }

        // формат номера
        for ($i = 2; $i < $number_of_cells_for_validation; $i++) {
            $sheet->getStyle('E' . $i)
                ->getNumberFormat()
                ->setFormatCode(
                    '00000000000'
                );
        }

        //todo валидацию возрасту

        // название файла
        $file_name = "Пример_пользователей_";

        // названия столбцов
        $sheet->fromArray(
            [
                'Username*',
                'Пол*',
                'Возраст*',
                'ID региона*',
                'Номер телефона*',
            ],
            null,
            'A1'
        );

        // первая строчка
        $sheet->fromArray(
            [
                'user244235',
                'Мужской',
                '23',
                (string)$regions[0],
                '79999999999'
            ],
            null,
            'A2'
        );

        // вторая строчка
        $sheet->fromArray(
            [
                'user244235',
                'Мужской',
                '23',
                (string)$regions[1],
                '79999999991'
            ],
            null,
            'A2'
        );

        //создаём и отдаём файл

        $streamedResponse = new StreamedResponse();
        $streamedResponse->setCallback(
            function () use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            }
        );
        $streamedResponse->setStatusCode(200);
        $streamedResponse->headers->set(
            'Content-Type',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );
        $streamedResponse->headers->set(
            'Content-Disposition',
            'attachment; filename="' . $file_name . '.xlsx"'
        );

        return $streamedResponse->send();
    }
}
