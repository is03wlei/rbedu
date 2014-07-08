/**
*资料上传
*by baixu
*/

console.log('upload begin');

$(document).ready(
	function(){
		
		var fileName = '';
		
		var uploader = WebUploader.create({

			// swf文件路径
			swf: '/js/Uploader.swf',

			// 文件接收服务端。
			server: '/savepic',

			// 选择文件的按钮。可选。
			// 内部根据当前运行是创建，可能是input元素，也可能是flash.
			pick: '#picker',

			// 不压缩image, 默认如果是jpeg，文件上传前会压缩一把再上传！
			resize: false
		});
		
		// 当有文件添加进来的时候
		uploader.on( 'fileQueued', function( file ) {
			$('#thelist').html( '<div id="' + file.id + '" class="item">' +
				'<h4 class="info">' + file.name + '</h4>' +
				'<p class="state">等待上传...</p>' +
			'</div>' );
			uploader.upload();
		});
		uploader.on( 'uploadSuccess', function( file,response ) {
			$('#file-error').hide();
			fileName = response._raw;
			$( '#'+file.id ).find('p.state').text('已上传');
		});

		uploader.on( 'uploadError', function( file ) {
			fileName = '';
			$( '#'+file.id ).find('p.state').text('上传出错');
		});
		
		
		
		$('#material-info').validate({
			errorElement: 'div', //default input error message container
			errorClass: 'help-block', // default input error message class
			focusInvalid: false, // do not focus the last invalid input
			rules: {
				'title': {
					required: true
				},
				'description': {
					required: true
				}
			},

			messages: {
				'title': {
					required: "请输入资料名称"
				},
				'description': {
					required: "请输入资料描述"
				}
			},

			invalidHandler: function (event, validator) { //display error alert on form submit   
				
			},

			highlight: function (element) { // hightlight error inputs
				
			},

			success: function (label) {
				label.remove();
			},

			errorPlacement: function (error, element) {
				error.insertAfter(element);
			},

			submitHandler: function (form) {
				//console.log('succ');
				//form.submit(); // form validation success, call ajax form submit
			}
		});
		
		var checkFile = function(){
			if(fileName){
				$('#file-error').hide();
				return true;
			}else{
				$('#file-error').show();
				return false;
			}
		}
		
		var submitHandler = function(e){
			if($('#material-info').validate().form() && checkFile()){
				var level = $('[name=level]:checked').attr('value');
				var materialType = $('[name=materialType]:checked').attr('value');
				$.ajax({
					cache: false,
					type: "POST",
					url:"/material/publish",
					data:{level:level,materialType:materialType,KID:1,KnowlegeName:'默认名称',title:$('#title').val(),description:$('#description').text(),url:fileName},	 
					async: false,
					error: function(request) {
						console.log(request)
						//alert("发送请求失败！");
					},
					success: function(data) {
						console.log(data);
						
					}
				});
			}
		};
		
		$('#submit-return').click(function(e){
			submitHandler(e);
		});
		
		$('#submit-continue').click(function(e){
			submitHandler(e);
		});
		
	}
);
