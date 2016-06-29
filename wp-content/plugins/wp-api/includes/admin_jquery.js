$(document).ready(function() {
	$('#p,#p1,#p2,#p3').hide();
	$('#t,#t1,#t2,#t3').hide();
	$('#a,#a1,#a2,#a3').hide();
	$('#g,#g1,#g2,#g3').hide();
	$('#s,#s1,#s2,#s3').hide();
	$('#c,#c1,#c2,#c3').hide();
	$('#i_p').click(function() {
		$('#p,#p1,#p2,#p3').show();
		$('#t,#t1,#t2,#t3').hide();
		$('#a,#a1,#a2,#a3').hide();
		$('#g,#g1,#g2,#g3').hide();
		$('#s,#s1,#s2,#s3').hide();
		$('#c,#c1,#c2,#c3').hide();
	});
	$('#i_t').click(function() {
		$('#p,#p1,#p2,#p3').hide();
		$('#t,#t1,#t2,#t3').show();
		$('#a,#a1,#a2,#a3').hide();
		$('#g,#g1,#g2,#g3').hide();
		$('#s,#s1,#s2,#s3').hide();
		$('#c,#c1,#c2,#c3').hide();
	});
	$('#i_a').click(function() {
		$('#p,#p1,#p2,#p3').hide();
		$('#t,#t1,#t2,#t3').hide();
		$('#a,#a1,#a2,#a3').show();
		$('#g,#g1,#g2,#g3').hide();
		$('#s,#s1,#s2,#s3').hide();
		$('#c,#c1,#c2,#c3').hide();
	});
	$('#i_g').click(function() {
		$('#p,#p1,#p2,#p3').hide();
		$('#t,#t1,#t2,#t3').hide();
		$('#a,#a1,#a2,#a3').hide();
		$('#g,#g1,#g2,#g3').show();
		$('#s,#s1,#s2,#s3').hide();
		$('#c,#c1,#c2,#c3').hide();
	});
	$('#i_s').click(function() {
		$('#p,#p1,#p2,#p3').hide();
		$('#t,#t1,#t2,#t3').hide();
		$('#a,#a1,#a2,#a3').hide();
		$('#g,#g1,#g2,#g3').hide();
		$('#s,#s1,#s2,#s3').show();
		$('#c,#c1,#c2,#c3').hide();
	});
	$('#i_c').click(function() {
		$('#p,#p1,#p2,#p3').hide();
		$('#t,#t1,#t2,#t3').hide();
		$('#a,#a1,#a2,#a3').hide();
		$('#g,#g1,#g2,#g3').hide();
		$('#s,#s1,#s2,#s3').hide();
		$('#c,#c1,#c2,#c3').show();
	});
});