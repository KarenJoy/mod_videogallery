function loadVideo(myFile,myImage,fid) { 
			playerInstance.load([{
			  file: myFile,
			  image: myImage
			}]);
			playerInstance.play();
			document.getElementById('fileid').value = fid;
			var newArray = [];
			var output = '';
			$.ajax({
                    url: 'loadComments.php',
                    data: {fileid: fid},
                    type: 'POST', 
                    success:function(output){
                    	
                if (output=='null') {                	
                	
                	$('#comments-container').empty();

                	
					       	
                } else {
                	//alert(output);
                	$('#comments-container').comments({
					profilePictureURL: 'https://viima-app.s3.amazonaws.com/media/user_profiles/user-icon.png',
					roundProfilePictures: true,
					textareaRows: 1,
					enableAttachments: false,

					getComments: function(success, error) {
						setTimeout(function() {
							
							success(JSON.parse(output));
							
						}, 500);
					},
					postComment: function(data, success, error) {
						setTimeout(function() {
							success(data);
						}, 500);
					},
					putComment: function(data, success, error) {
						setTimeout(function() {
							success(data);
						}, 500);
					},
					deleteComment: function(data, success, error) {
						setTimeout(function() {
							success();
						}, 500);
					},
					upvoteComment: function(data, success, error) {
						setTimeout(function() {
							success(data);
						}, 500);
					},
					uploadAttachments: function(dataArray, success, error) {
						setTimeout(function() {
							success(dataArray);
						}, 500);
					},
					
					});
                	}
				}
			});		 

		  };