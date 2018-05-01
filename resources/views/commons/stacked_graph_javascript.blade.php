@if (!empty($graph_title))
<script>
  var ctx = document.getElementById("myChart").getContext('2d');
      // 20色
      var colors = ['rgba(70,132,238,0.8)', 'rgba(220,57,18,0.8)', 'rgba(255,153,0,0.8)', 'rgba(0,128,0,0.8)', 'rgba(73,66,204,1.0)', 'rgba(229,46,184,0.8)', 'rgba(140,140,140,0.8)', 'rgba(46,115,229,0.5)', 'rgba(220,57,18,0.5)', 'rgba(255,173,51,0.5)', 'rgba(51,153,51,0.5)', 'rgba(73,66,204,0.5)', 'rgba(234,88,198,0.5)', 'rgba(140,140,140,0.5)', 'rgba(150,185,242,1.0)', 'rgba(220,57,18,0.2)', 'rgba(255,173,51,0.2)', 'rgba(51,153,51,0.2)', 'rgba(73,66,204,0.2)', 'rgba(234,88,198,0.2)'];
   	  var myChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: [
          @foreach ($graph_labels as $graph_label)
          "{{$graph_label}}", 
        @endforeach
        ],
        datasets: [
          @foreach ($graph_datasets as $graph_dataset)
          {
            label: "{{$graph_dataset['NAME']}}",
            borderWidth:1,
            backgroundColor: ""+ colors[{{$graph_dataset['POS']}}] +"",
            data: [
              @foreach($graph_dataset['DATA'] as $graph_data)
                {{$graph_data}},
              @endforeach
            ]
          },
          @endforeach
        ]
      },
    options: {
          title: {
              display: true,
              text: '{{$graph_title}}', //グラフの見出し
              padding: 3
          },
          scales: {
              xAxes: [{
                    stacked: true, //積み上げ棒グラフにする設定
                    categoryPercentage:0.4 //棒グラフの太さ
              }],
              yAxes: [{
                    stacked: true //積み上げ棒グラフにする設定
              }]
          },
          legend: {
              labels: {
                    boxWidth: 20,
                    fontSize: 11,
                    padding: 10 //凡例の各要素間の距離
              },
              display: true
          },
          tooltips: {
            mode:'label' //マウスオーバー時に表示されるtooltip
          }
       }
    });
  </script>
@endif