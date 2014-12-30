
function evol_recettes(donnees)
{
	chart_recette = new Highcharts.Chart(
	{
	 chart: 
	 {
		renderTo: 'evol_recettes',
		defaultSeriesType: 'column'
	 },
	 title: {
		text: 'Recettes'
	 },
	 xAxis: {type: 'datetime'},
	 yAxis: {min : 0},
	 plotOptions:
	 {
		column:
		{
			stacking: 'normal'
		}
	},
	 series: donnees
  });
}

function repart_recettes(donnees)
{
	chart_recette_total = new Highcharts.Chart({
	 chart: {
		renderTo: 'repart_recettes',
		plotBackgroundColor: null,
     plotBorderWidth: null,
     plotShadow: false
	 },
	 title: {
		text: 'Recettes totales'
	 },
	 xAxis: {categories:'Total'},
	 yAxis: {min : 0},
	 tooltip: {
         formatter: function() {
		    return ''+
			this.series.name +': '+ this.y +' ('+ Math.round(this.percentage) +'%)';
		}
	},
	 plotOptions: {
     pie: {
        allowPointSelect: true,
        cursor: 'pointer',
        dataLabels: {
           enabled: true,
           formatter: function() {
              return '<b>'+ this.point.name +'</b>: '+ this.y;
           }
        }
     }
  },
	 series: [{type: 'pie', data : donnees}]
  });
}