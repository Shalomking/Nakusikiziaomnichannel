<!DOCTYPE html>

<div id="chart"></div>

<script src="{_assets("js/libs/fetch.min.js")}"></script>
<script>
	fetchInject([
		"{_assets("js/functions.js")}"
	], fetchInject([
		"{_assets("js/libs/apexcharts.min.js")}",
		"{_assets("js/libs/iframeResizer.contentWindow.min.js")}"
	])).then(() => {
		zender.charts("{$chart}");
	});
</script>