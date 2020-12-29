<?php
session_start(); 
include_once("./config/connectDB.php"); 
if(isset($_GET["action"])) {
switch($_GET["action"]) {
	case "add":
		if(isset($_POST["quantity"])) {
            $sql="SELECT * FROM tblproduct WHERE code='{$_GET["code"]}'";
            $result = mysqli_query($conn,$sql);
            $productByCode = mysqli_query($conn,$sql);
			 
			$itemArray;
			while($data = mysqli_fetch_assoc($result)) {
                $itemArray = [ 
                    $data["code"]=>array('name'=>$data["name"], 'code'=>$data["code"], 'quantity'=>$_POST["quantity"], 'price'=>$data["price"], 'image'=>$data["image"])
                ];
                if(isset($_SESSION["cart_item"])) {
                    if(in_array($data["code"],array_keys($_SESSION["cart_item"]))) {
                        foreach($_SESSION["cart_item"] as $k => $v) {
                                if($data["code"] == $k) {
                                    if(empty($_SESSION["cart_item"][$k]["quantity"])) {
                                        $_SESSION["cart_item"][$k]["quantity"] = 0;
                                    }
                                    $_SESSION["cart_item"][$k]["quantity"] += $_POST["quantity"];
                                }
                        }
                    } else {
                        $_SESSION["cart_item"] = array_merge($_SESSION["cart_item"],$itemArray);
                    }
                } else {
                    $_SESSION["cart_item"] = $itemArray;
                }
            }
		}
	break;
	case "remove":
		if(isset($_SESSION["cart_item"])) {
			foreach($_SESSION["cart_item"] as $k => $v) {
					if($_GET["code"] == $k)
						unset($_SESSION["cart_item"][$k]);				
					if(empty($_SESSION["cart_item"]))
						unset($_SESSION["cart_item"]);
			}
		}
	break; 	
}
}
?>
<HTML>

<HEAD>
    <TITLE>Simple PHP Shopping Cart</TITLE>
    <link href="style.css" type="text/css" rel="stylesheet" />
</HEAD>

<BODY>
    <div id="shopping-cart">
        <div class="txt-heading">Shopping Cart</div>

        <?php
if(isset($_SESSION["cart_item"])){
    $total_quantity = 0;
    $total_price = 0;
?>
        <table class="tbl-cart" cellpadding="10" cellspacing="1">
            <tbody>
                <tr>
                    <th style="text-align:left;">Name</th>
                    <th style="text-align:left;">Code</th>
                    <th style="text-align:right;" width="5%">Quantity</th>
                    <th style="text-align:right;" width="10%">Unit Price</th>
                    <th style="text-align:right;" width="10%">Price</th>
                    <th style="text-align:center;" width="5%">Remove</th>
                </tr>
                <?php	
                
    foreach ($_SESSION["cart_item"] as $item){
        $item_price = $item["quantity"]*$item["price"];
		?>
                <tr>
                    <td><img src="./product-images/<?php echo $item["image"]; ?>"
                            class="cart-item-image" /><?php echo $item["name"]; ?></td>
                    <td><?php echo $item["code"]; ?></td>
                    <td style="text-align:right;"><?php echo $item["quantity"]; ?></td>
                    <td style="text-align:right;"><?php echo "$ ".$item["price"]; ?></td>
                    <td style="text-align:right;"><?php echo "$ ". number_format($item_price,2); ?></td>
                    <td style="text-align:center;"><a href="order.php?action=remove&code=<?php echo $item["code"]; ?>"
                            class="btnRemoveAction"><img src="icon-delete.png" alt="Remove Item" /></a>
                    </td>
                </tr>
                <?php
				$total_quantity += $item["quantity"];
				$total_price += ($item["price"]*$item["quantity"]);
		}
		?>

                <tr>
                    <td colspan="2" align="right">Total:</td>
                    <td align="right"><?php echo $total_quantity; ?></td>
                    <td align="right" colspan="2"><strong><?php echo "$ ".number_format($total_price, 2); ?></strong>
                    </td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <?php
} else {
?>
        <div class="no-records">Your Cart is Empty</div>
        <?php 
}
?>
    </div>


    </div>
</BODY>

</HTML>