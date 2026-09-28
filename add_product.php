<link rel="stylesheet" href="libs/css/main2.css" />
<?php 
  $page_title = 'Add Asset';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(2);
  $all_categories = find_all('categories');
  $all_photo = find_all('media');
  $all_users = find_all_user();
?>
<?php
 if(isset($_POST['add_product'])){
   $req_fields = array('product-title','product-categorie','product-quantity','buying-price','user_name');
   validate_fields($req_fields);
   if(empty($errors)){
     $p_name  = remove_junk($db->escape($_POST['product-title']));
     $p_cat   = remove_junk($db->escape($_POST['product-categorie']));
     $p_qty   = remove_junk($db->escape($_POST['product-quantity']));
     $p_buy   = remove_junk($db->escape($_POST['buying-price']));
     $p_a_user = (int)($_POST['user_name'] ?? 0);
     if ($p_a_user <= 0) {
       $session->msg('d', 'Please select a valid user.');
       redirect('add_product.php', false);
     }
     if (is_null($_POST['product-photo']) || $_POST['product-photo'] === "") {
       $media_id = '0';
     } else {
       $media_id = remove_junk($db->escape($_POST['product-photo']));
     }
     $date    = make_date();
     $columns = ['name','quantity','buy_price','categorie_id','media_id','date'];
     $values = ["'{$p_name}'", "'{$p_qty}'", "'{$p_buy}'", "'{$p_cat}'", "'{$media_id}'", "'{$date}'"];

     if (columnExists('products', 'user_name')) {
       $columns[] = 'user_name';
       $values[] = "'{$p_a_user}'";
     } elseif (columnExists('products', 'user_id') && !empty($p_a_user)) {
       $columns[] = 'user_id';
       $values[] = "'{$p_a_user}'";
     }

     $query  = "INSERT INTO products (";
     $query .= implode(',', $columns);
     $query .= ") VALUES (";
     $query .= implode(',', $values);
     $query .= ")";
     if($db->query($query)){
       $session->msg('s',"Asset added ");
       redirect('add_product.php', false);
     } else {
       $session->msg('d',' Sorry failed to added!');
       redirect('product.php', false);
     }

   } else{
     $session->msg("d", $errors);
     redirect('add_product.php',false);
   }

 }

?>
<?php include_once('layouts/header.php'); ?>
<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>
  <div class="row">
  <div class="col-md-8">
      <div class="panel panel-default">
        <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-th"></span>
            <span>Add New Asset</span>
         </strong>
        </div>
        <div class="panel-body">
         <div class="col-md-12">
          <form method="post" action="add_product.php" class="clearfix">
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon">
                   <i class="glyphicon glyphicon-th-large"></i>
                  </span>
                  <input type="text" class="form-control" name="product-title" placeholder="Product Title">
               </div>
              </div>
              <div class="form-group">
                <div class="row">
                  <div class="col-md-6">
                    <select class="form-control" name="product-categorie">
                      <option value="">Select Asset Category</option>
                    <?php  foreach ($all_categories as $cat): ?>
                      <option value="<?php echo (int)$cat['id'] ?>">
                        <?php echo $cat['name'] ?></option>
                    <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <select class="form-control" name="user_name">
                      <option value="">Select User</option>
                    <?php  foreach ($all_users as $a_user): ?>
                      <option value="<?php echo (int)$a_user['id']; ?>">
                        <?php echo htmlspecialchars((string)($a_user['name'] ?? $a_user['username'] ?? 'Unknown'), ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php endforeach; ?>
                    </select>
                  </div><br><br>   
                  <div class="col-md-6">
                    <select class="form-control" name="product-photo">
                      <option value="">Select Asset Photo</option>
                    <?php  foreach ($all_photo as $photo): ?>
                      <option value="<?php echo (int)$photo['id'] ?>">
                        <?php echo $photo['file_name'] ?></option>
                    <?php endforeach; ?>
                    </select>
                  </div>
                </div>
              </div>

              <div class="form-group">
               <div class="row">
                 <div class="col-md-4">
                   <div class="input-group">
                     <span class="input-group-addon">
                      <i class="glyphicon glyphicon-shopping-cart"></i>
                     </span>
                     <input type="number" class="form-control" name="product-quantity" placeholder="Product Quantity">
                  </div>
                 </div>
                 <div class="col-md-4">
                   <div class="input-group">
                     <span class="input-group-addon">
                       <i class="glyphicon glyphicon-usd"></i>
                     </span>
                     <input type="number" class="form-control" name="buying-price" placeholder="Buying Price">
                     <span class="input-group-addon">.00</span>
                  </div>
                 </div>
                  
               </div>
              </div>
              <div class="assetbtn">
                <button type="submit" name="add_product" class="btn btn-danger">Add Asset</button>
              </div>
          </form>
         </div>
        </div>
      </div>
    </div>
  </div>

<?php include_once('layouts/footer.php'); ?>
