
	<?php 
		include("side.php"); 
		$id = $_GET["qt_id"];
		$update = $db_con->prepare("UPDATE webboard_post SET VISIT_COUNT = (VISIT_COUNT+1) WHERE ID = ? ");
		$update->bindParam("1",$id);
		$result =  $update->execute();
		$question = $db_con->prepare("SELECT pf.POST_ID,post.BODY,pf.MIME_TYPE,post.TITLE,pf.DETAIL, pf.PATH_FILE,pf.DISPLAY_NAME ,pf.FILE_NAME,pf.FILE_SIZE
		FROM webboard_post post LEFT JOIN webboard_post_file pf ON pf.POST_ID=post.ID 
		WHERE post.ID = '".$_GET["qt_id"]."'"); 
		$question->execute();
		$row=$question->fetch(PDO::FETCH_ASSOC);

		if(isset($_GET["del"])){
			$del = $db_con->prepare("DELETE FROM webboard_comment WHERE ID = '".$_GET["del"]."' ");
			$del->execute();
			header("Location:question_reply.php?qt_id=". $_GET["qt_id"]);
		}
		if(isset($_GET["de"])){
			$del = $db_con->prepare("DELETE FROM webboard_comment WHERE ID = '".$_GET["de"]."' ");
			$del->execute();
			header("Location:question_reply.php?qt_id=". $_GET["qt_id"]);
		}
	?>
	<link rel="stylesheet" href="css/btn.css">
	<script src="//tinymce.cachefly.net/4.0/tinymce.min.js"></script>
	<script>
          tinymce.init({
              selector: "textarea",
              });
  	</script>
	<style>
	
	</style>
<body>
	
	<div id="content" style="padding-top: 100px">
		<div class="row">
			<div class="col-md-12">
				<h3><?php echo $row["TITLE"];?></h3>
				<p><?php echo $row["BODY"];?></p>
				
				<h4>	ไฟล์ที่เกี่ยวข้อง : </h4>
				<?php
				do {
					if (strlen($row["MIME_TYPE"]) ==0){
						echo ("ไม่มีไฟล์ที่เกี่ยวข้อง". $row["DETAIL"]);
					}elseif($row["MIME_TYPE"]=="jpg" || $row["MIME_TYPE"]=="gif" || $row["MIME_TYPE"]=="PNG"|| $row["MIME_TYPE"]=="png"){
						?>
						
							<a href= "<?php echo $row["PATH_FILE"];?>" download="<?php echo $row["DISPLAY_NAME"];?>">
							<img  src="<?php echo $row["PATH_FILE"];?>" alt="<?php echo $row["FILE_NAME"];?>" width="200" height="auto"><br></a>
						<br>
						<?php
					}elseif($row["MIME_TYPE"]=="xlsx"){
						?><a href= "<?php echo $row["PATH_FILE"];?>" download="<?php echo $row["DISPLAY_NAME"];?>">
						<img src="assets/xls.ico" alt="<?php echo $row["FILE_NAME"];?>" width="50" height="50">  <?php echo $row["DISPLAY_NAME"];?><br></a>
						<?php
					}elseif($row["MIME_TYPE"]=="docx"){
						?><a href= "<?php echo $row["PATH_FILE"];?>" download="<?php echo $row["DISPLAY_NAME"];?>">
						<img src="assets/docx.png" alt="<?php echo $row["FILE_NAME"];?>" width="50" height="50">  <?php echo $row["DISPLAY_NAME"];?><br></a>
						<?php
					}elseif($row["MIME_TYPE"]=="pdf"){
						?><a href= "<?php echo $row["PATH_FILE"];?>" download="<?php echo $row["DISPLAY_NAME"];?>">
						<img src="assets/Pdf.ico" alt="<?php echo $row["FILE_NAME"];?>" width="50" height="50">  <?php echo $row["DISPLAY_NAME"];?><br></a>
						<?php
					}elseif($row["MIME_TYPE"]=="txt"){
						?><a href= "<?php echo $row["PATH_FILE"];?>" download="<?php echo $row["DISPLAY_NAME"];?>">
						<img src="assets/txt.png" alt="<?php echo $row["FILE_NAME"];?>" width="50" height="50">  <?php echo $row["DISPLAY_NAME"];?><br></a>
						<?php
					}else{
						?><a href= "<?php echo $row["PATH_FILE"];?>" download="<?php echo $row["DISPLAY_NAME"];?>">
						<img src="assets/file.png" alt="<?php echo $row["FILE_NAME"];?>" width="50" height="50">  <?php echo $row["DISPLAY_NAME"];?><br></a>
						<?php
					}
				}while ($row=$question->fetch(PDO::FETCH_ASSOC));
				?>

			</div>
		</div>
		<hr>
		<div class="row">
			<div class="col-md-12">
				<?php 
					$l = 1;
					$reply = $db_con->prepare("SELECT * FROM webboard_comment WHERE POST_ID = '".$_GET["qt_id"]."' and COMMENT_ID is null ORDER BY POST_ID DESC");
					$reply->execute();

					while ($row2=$reply->fetch(PDO::FETCH_ASSOC)) {
				?>

				<div class="panel panel-default">
				  <div class="panel-heading"><strong> (ชือผู้ตอบ <?php echo ucwords($row2["CREATED_USER"]); ?>)</strong></div>
				  <div class="panel-body">
				    <?php echo $row2["BODY"]; ?>
						<p>&nbsp;</p>
							<small>สร้างเมื่อ <?php echo $row2["CREATED_DATE"]; ?></small>
							<?php 
								$reply2 = $db_con->prepare("SELECT * FROM webboard_comment WHERE POST_ID = '".$_GET["qt_id"]."' and COMMENT_ID = '".$row2["ID"]."' ORDER BY ID DESC");
								$reply2->execute();

								while ($row3=$reply2->fetch(PDO::FETCH_ASSOC)) {
							?>
								<div class="row">
									<div class="col-md-6">	
										<div class="panel panel-default">
											<div class="panel-heading"><strong>ชือผู้ตอบ <?php echo ucwords($row3["CREATED_USER"]); ?></strong> <?php echo " ". $row3["BODY"]; ?>
											<small>สร้างเมื่อ <?php echo $row3["CREATED_DATE"]; ?></small>

											<?php 
											if(isset($_SESSION["member_name"])){
											if($_SESSION["member_name"]==$row3["CREATED_USER"]){
											?>
											<div class="col-md-1">
												<div class="dropdown dropright">
													<button type="button" class="btn btn-outline-secondary" data-toggle="dropdown">ตัวเลือก</button>
													<div class="dropdown-menu">
														<a class="dropdown-item" id="edit_ment<?php echo $row3["ID"]; ?>" onclick="fn_toggle3(this)" >แก้ไข</a>
														<a class="dropdown-item" href="question_reply.php?qt_id=<?php echo $_GET["qt_id"]; ?>&de=<?php echo $row3["ID"]; ?>" onclick="return confirm('ท่านต้องการลบแถวนี้ใช่หรือไม่');" role="button">ลบ</a>
													</div>
												</div>
											</div>
											<div class="col-md-12">
												<form method="post" id="form5_<?php echo $row3["ID"]; ?>" class = "form5" action="comment_edit_send.php">
													<div class="form-group">
															<label>แก้ไขความคิดเห็น</label>
															<textarea class="form-control" id="mentEdit<?php echo $row3["ID"]; ?>" name="mentEdit" ><?php echo $row3["BODY"];?></textarea>
														</div>
														<input type="hidden" name="qt_id" id="qt_id" value="<?php echo $_GET["qt_id"]; ?>">
														<input type="hidden" name="ment_id" value="<?php echo $row3["ID"]; ?>">
													<div class="row">
														<div class="col-md-2">
															<button type="submit" class="btn btn-primary">บันทึก</button>
														</div>
													</div>
												</form>
											</div>
											<?php
											}
											}
											?>
										</div>
										</div>
									</div>
								</div>
							<?php
								}
							?>			
							<div class="row">
								<?php 
									if(isset($_SESSION["member_name"])){
								?>
								<div class="col-md-1">
									<button type="button" class="btn btn-outline-secondary" id="showment<?php echo $row2["ID"]; ?>" onclick="fn_toggle2(this)" >ตอบกลับ</button>
								</div>
								<?php
									}
								?>
								<?php 
									if(isset($_SESSION["member_name"])){
									if($_SESSION["member_name"]==$row2["CREATED_USER"]){
								?>
								<div class="col-md-1">
									<div class="dropdown dropright">
										<button type="button" class="btn btn-outline-secondary" data-toggle="dropdown">ตัวเลือก</button>
										<div class="dropdown-menu">
											<a class="dropdown-item" id="edit<?php echo $row2["ID"]; ?>" onclick="fn_toggle(this)" >แก้ไข</a>
											<a class="dropdown-item" href="question_reply.php?qt_id=<?php echo $_GET["qt_id"]; ?>&del=<?php echo $row2["ID"]; ?>" onclick="return confirm('ท่านต้องการลบแถวนี้ใช่หรือไม่');" role="button">ลบ</a>
										</div>
									</div>
								</div>
								<?php
								}
								}
								
								?>
							</div>


							<div class="row">
								<div class="col-md-12">
									<form method="post" id="form4_<?php echo $row2["ID"]; ?>" class = "form4" action="question_reply_send2.php">
										<div class="form-group">
											<label>แสดงความคิดเห็น</label>
											<textarea class="form-control" id="ment_2<?php echo $row2["ID"]; ?>" name="ment_2" ></textarea>
										</div>
											<input type="hidden" name="qt_id" id="qt_id" value="<?php echo $_GET["qt_id"]; ?>">
											<input type="hidden" name="ment_id" value="<?php echo $row2["ID"]; ?>">
										<div class="row">
											<div class="col-md-2">
												<button type="submit" class="btn btn-primary">บันทึก</button>
											</div>
										</div>
									</form>
								</div>
								<?php 
								if(isset($_SESSION["member_name"])){
								if($_SESSION["member_name"]==$row2["CREATED_USER"]){
								?>
								
								<br>
								<div class="col-md-12">
									<form method="post" id="form3_<?php echo $row2["ID"]; ?>" class = "form3" action="comment_edit_send.php">
										<div class="form-group">
												<label>แก้ไขความคิดเห็น</label>
												<textarea class="form-control" id="mentEdit<?php echo $row2["ID"]; ?>" name="mentEdit" ><?php echo $row2["BODY"];?></textarea>
											</div>
											<input type="hidden" name="qt_id" id="qt_id" value="<?php echo $_GET["qt_id"]; ?>">
											<input type="hidden" name="ment_id" value="<?php echo $row2["ID"]; ?>">
										<div class="row">
											<div class="col-md-2">
												<button type="submit" class="btn btn-primary">บันทึก</button>
											</div>
										</div>
									</form>
								</div>
									
								<?php
								}
								}
								?>
							</div>
					</div>
				
				</div>
				<?php 
						}
				?>
			</div>
		</div>
		<?php
		if(isset($_SESSION["member_name"])){
			?>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-primary">
				  <div class="panel-heading">แสดงความคิดเห็น</div>
					<div class="panel-body">
						<form method="post" id="form2" action="question_reply_send.php">
							<label>ผู้ตอบกลับ <?php echo $_SESSION["member_name"]; ?></label>
							<input type="hidden" name="member_name" value="<?php echo $_SESSION["member_name"];?>">
							<div class="form-group">
								<textarea class="form-control" id="rp_detail" name="rp_detail" rows="3" placeholder="ระบุรายละเอียด"></textarea>
							</div>
							<input type="hidden" name="qt_id" value="<?php echo $_GET["qt_id"];?>">
								<button type="submit" class="btn btn-primary">บันทึก</button>
								<a href="question_me.php?page=1" role="button" class=" btn btn-default">ยกเลิก</a>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<?php
		}
		?>
		<hr>
	</div>

</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
$(document).ready(function(){
    $("#form2").submit(function(e){
        var inp = $("#rp_detail");
        if (inp.val().length == 0) {  
            alert("กรุณากรอกข้อมูล");
        	e.preventDefault();
        }
    });
});
</script>
<script>
$(document).ready(function(){
	$(".form3").hide();

});
</script>

<script>
function fn_toggle(id) {
	
		var str1 = id.getAttribute('id');
		var str_id = str1.substring(4, str1.length);
		var x = document.getElementById("form3_"+str_id);
    if (x.style.display === "none") {
        x.style.display = "block";
    } else {
        x.style.display = "none";
    }
   
}
</script>

<script>
$(document).ready(function(){
	$(".form4").hide();

});
</script>

<script>
function fn_toggle2(id) {
	
		var str1 = id.getAttribute('id');
		var str_id = str1.substring(8, str1.length);
		
		var x = document.getElementById("form4_"+str_id);
    if (x.style.display === "none") {
        x.style.display = "block";
    } else {
        x.style.display = "none";
    }
   
}
</script>
<script>
$(document).ready(function(){
	$(".form5").hide();

});
</script>

<script>
function fn_toggle3(id) {
	
		var str1 = id.getAttribute('id');
		var str_id = str1.substring(9, str1.length);
		
		var x = document.getElementById("form5_"+str_id);
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
		
		}
</script>
<script src = "use_side.js"></script>

