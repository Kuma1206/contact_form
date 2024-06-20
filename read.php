<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>お問い合わせ一覧</title>
    <link rel="stylesheet" type="text/css" href="css/read.css" />
</head>
<body>

<main>
    <fieldset>
        <!-- <legend>管理画面</legend> -->
        <!-- <a href="todo_txt_input.php">入力画面</a> -->
        <!-- フィルタリング用のプルダウンメニュー -->
        <div>
            <label for="filter-goiken">カテゴリ検索:</label>
            <select id="filter-goiken">
                <option value="">すべてのご意見</option>
                <option value="サービス全般">サービス全般</option>
                <option value="機能について">機能について</option>
                <option value="プランについて">プランについて</option>
                <option value="その他ご要望">その他ご要望</option>
            </select>
        </div>
        <table>
            <thead>
                <tr>
                    <th id="th-name">お名前</th>
                    <th id="th-mail">メールアドレス</th>
                    <th id="th-iken">ご意見</th>
                    <th id="th-info">お問合せ内容</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // タグの文字列を入れる変数
                $str = '';

                // ファイルを開く（読み取り専用）
                $file = fopen('data/info.csv', 'r');
                // ファイルをロック
                flock($file, LOCK_EX);

                // fgetcsv()で1行ずつ取得→$lineに格納
                if ($file) {
                    while ($line = fgetcsv($file)) {
                        $goiken = $line[2]; // ご意見の列を取得
                        $str .= "<tr data-goiken='{$goiken}'>";
                        foreach ($line as $value) {
                            $str .= "<td>{$value}</td>";
                        }
                        $str .= "</tr>";
                    }
                }

                // ロックを解除する
                flock($file, LOCK_UN);
                // ファイルを閉じる
                fclose($file);

                echo $str;
                ?>
            </tbody>
        </table>
    </fieldset>

    <section id="data">
        <div id="g-bou">
            <canvas id="myChart"></canvas>
        </div>
        <div id="g-en">
            <canvas id="myGurafu">テスト</canvas>
        </div>
    </section>

</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    // プルダウンメニューで選択した項目に基づいて表をフィルタリングする
    $('#filter-goiken').on('change', function() {
        var selectedGoiken = $(this).val();
        if (selectedGoiken) {
            $('tbody tr').hide().filter(`[data-goiken="${selectedGoiken}"]`).show();
        } else {
            $('tbody tr').show();
        }
    });

    // ご意見の数を集計する
    const goikenCounts = {
        'サービス全般': 0,
        '機能について': 0,
        'プランについて': 0,
        'その他ご要望': 0
    };

    $('tbody tr').each(function() {
        const goiken = $(this).data('goiken');
        if (goikenCounts[goiken] !== undefined) {
            goikenCounts[goiken]++;
        }
    });

    const barCtx = document.getElementById('myChart');
    const pieCtx = document.getElementById('myGurafu');

    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: ['サービス全般', '機能について', 'プランについて', 'その他ご要望'],
            datasets: [{
                label: 'カテゴリ別問合せ件数',
                data: [
                    goikenCounts['サービス全般'],
                    goikenCounts['機能について'],
                    goikenCounts['プランについて'],
                    goikenCounts['その他ご要望']
                ],
                    backgroundColor: [
                        'rgba(255, 105, 163, 0.9)',
                        'rgba(54, 162, 235, 0.9)',
                        'rgba(255, 206, 86, 0.9)',
                        'rgba(75, 192, 192, 0.9)'
                    ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: [
                'サービス全般',
                '機能について',
                'プランについて',
                'その他ご要望'
            ],
            datasets: [{
                label: 'ご意見の数',
                data: [
                    goikenCounts['サービス全般'],
                    goikenCounts['機能について'],
                    goikenCounts['プランについて'],
                    goikenCounts['その他ご要望']
                ],
                backgroundColor: [
                    'rgb(255, 99, 132)',
                    'rgb(54, 162, 235)',
                    'rgb(255, 205, 86)',
                    'rgb(75, 192, 192)'
                ],
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            let dataset = tooltipItem.dataset;
                            let total = dataset.data.reduce((accumulator, currentValue) => accumulator + currentValue, 0);
                            let currentValue = dataset.data[tooltipItem.dataIndex];
                            let percentage = Math.round((currentValue / total) * 100);
                            return `${tooltipItem.label}: ${currentValue}件 (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });

</script>

</body>
</html>
