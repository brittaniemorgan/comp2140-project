<link rel="stylesheet" href="index.css">
<script src="script.js" type="text/javascript"></script>
<?php
#session_start();
#$_SESSION["user"] = "user123";
 
class DBManager{

    private $conn;
    private $host;
    private $username;
    private $password;
    private $dbname;
    

    function __construct($host, $username, $password, $dbname)
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->dbname = $dbname;

        
        date_default_timezone_set('EST');

        try{
            $this->conn = new PDO(
                'mysql:host=' . $this->host . ';dbname=' . $this->dbname,
                $this->username,
                $this->password
            );
            
        }catch(Exception $e){
            die($e->getMessage());
        }
        
        
    }

    function getConn(){
        return $this->conn;
    }
    #working
    function menuInfo(){
        #get menu items from the database
        $stmt = $this->conn->query("SELECT * FROM menuItems ORDER BY category");
        $results = $stmt->fetchAll();

        ?><div>
            
        <?php
            #iterates through each row of the data base
            for ($x = 0; $x < count($results); $x++){ 


                #prints item category when item is the first one in the category       
                if($x > 0 and $results[$x-1]['category'] != $results[$x]['category']){?>
                    <h3 class="category-heading"><?=$results[$x]['category']?></h3>
                <?php }elseif($x === 0){?>
                    <h3 class="category-heading"><?=$results[$x]['category']?></h3>
                <?php } ?>
                        
                
                <button class="addToOrderButton" id="<?=$results[$x]["id"]?>">
                    <div class="menuItem" id="<?=$results[$x]["id"]?>">
                        <img src=<?="images/".$results[$x]['image']?> class="menuItemPic" itemid="<?=$results[$x]["id"]?>">

                        <div class="menuItemContent"  itemid="<?=$results[$x]["id"]?>">
                            
                            <h5 itemid="<?=$results[$x]["id"]?>"><?=$results[$x]['name']?></h5>
                            <div class="prices" itemid="<?=$results[$x]["id"]?>">
                                <?php 
                                    #checks if the item comes in a large size and prints the large size value
                                    if(intval($results[$x]['large_price']) > 0 and intval($results[$x]['price']) > 0){?>
                                            
                                        <h6 itemid="<?=$results[$x]["id"]?>"><?=$results[$x]['medium_size']?> - $<?=$results[$x]['price']?></h6>
                                        <h6 itemid="<?=$results[$x]["id"]?>"><?=$results[$x]['large_size']?> - $<?=$results[$x]['large_price']?></h6>
                                            
                                            
                                    <?php }elseif(intval($results[$x]['price']) > 0){?>
                                             
                                        <h6 itemid="<?=$results[$x]["id"]?>">Price - $<?=$results[$x]['price']?></h6>
                                    <?php } ?>       
                                                 
                                    
                                </div>
                        </div>
                    </div>
                </button>
                        
                    <?php } ?>
        </div><?php
    }
    #stmt usually doesnt execute when the vallues we are trying to insert bigger than the value defined in the header insql
    #working
    function userInfo($name){
        

        $stmt = $this->conn->prepare("SELECT * FROM `users` WHERE `name` = :name");
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        
        if($stmt->execute()){


            if($stmt->rowCount() > 0 ){
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                #tester - later delete
                foreach($results as $row){
                    $info = $row;
                    echo $row['name'];
                }
                return $info;
            
            }else{
                return 'user not found';
            }

        }
    }

    
    #working
    function addUser($name, $password){
        
        $already_here = $this->userInfo($name);
        $hashPass = hash("sha512", $password);

        #constraint - choose a suitable size to limit password to to accomadate hash
        
        
        if($already_here === 'user not found'){

            $stmt = $this->conn->prepare("INSERT INTO `users` (`name`, `password`) VALUES (:name, :password)");
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':password', $hashPass, PDO::PARAM_STR);

            if($stmt->execute()){
                echo 'work';
            }else{
                echo 'an error occurred';
            }
            

        }else{
            echo 'someone already has this username';
        }
             
    }

    
    function deleteUser($name){
        
        $stmt = $this->conn->prepare("DELETE FROM `users` WHERE name = :name");
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);

        if($stmt->execute()){
            echo 'user deleted';
        }else{
            echo 'error, user not delete';
        }
    }

    
    function orderInfo($orderId){
        
        $stmt = $this->conn->prepare("SELECT * FROM `orders` WHERE `id` = :orderId");
        $stmt->bindParam(':orderId', $orderId, PDO::PARAM_INT);

        if($stmt->execute()){
            if($stmt->rowCount() > 0){
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach($results as $row){
                    $info = $row['items'];
                }
                
                return $info;

            }else{
                return 'couldnt find order';
            }

        }else{
            return 'an error';
        }

        #select last order - might need
        #$stmt = $this->conn->prepare("SELECT * FROM orders ORDER BY :id DESC LIMIT 1");
    }

    
   
    function addOrder($total, $items, $genLocation, $address){

        $date = date('d/M/Y');
        $start_time = date('h:i a');
        

        $stmt = $this->conn->prepare("INSERT INTO `orders` (`total`, `items`, `date`, `gen_del_location`, `address`, `start_time`) VALUES (:total, :items, :date, :genLocation, :address, :start_time)");
        $stmt->bindParam(':total', $total, PDO::PARAM_INT);
        $stmt->bindParam(':items', $items, PDO::PARAM_STR);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        $stmt->bindParam(':genLocation', $genLocation, PDO::PARAM_STR);
        $stmt->bindParam(':address', $address, PDO::PARAM_STR);
        $stmt->bindParam(':start_time', $start_time, PDO::PARAM_STR);

        if($stmt->execute()){
            echo 'order placed';
        }else{
            echo 'error, couldnt place order';
        }

    }

    
    function deleteOrder($orderId){
        $stmt = $this->conn->prepare("DELETE FROM `orders` WHERE `id` = :orderId");
        $stmt->bindParam(':orderId', $orderId, PDO::PARAM_STR);

        if($stmt->execute()){
            echo 'order deleted';
        }else{
            echo 'error, order deleted';
        }

    }


    function getFoodDescription($foodID){
        $stmt = $this->conn->query("SELECT * FROM menuItems WHERE id = $foodID");
        $results = $stmt->fetchAll();

        ?>
        <div>
            <h2><?= $results[0]["name"]?></h2>
            <form id="" action="viewCart.php" method="POST">
                <textarea name="foodID" style="display: none;"><?=$foodID?></textarea>
                <?php 
                    if ($results[0]["large_size"] != null):
                ?>
                    <p>Please select a meal size</p>
                    <input type="radio" name="mealSize" value="Medium" required> Medium - $<?=$results[0]["price"]?></input>
                    <input type="radio" name="mealSize" value="Large"> Large - $<?=$results[0]["large_price"]?></input>

                <?php else:?>
                    <p> Price $<?=$results[0]["price"]?></p>
                <?php endif; 
                    if ($results[0]["category"] == "breakfast"):
                ?>
                <p>Choose a side</p>
                    <input type="radio" name="side" value="toast">Toast</input>
                    <input type="radio" name="side" value="hash-browns">Hash Browns</input> 
                    <input type="radio" name="side" value="bagels">Bagels</input>
                    <input type="radio" name="side" value="french-toast">French Toast</input>   
                <?php endif?>            
                <label for="comments">Comments:</label>
                <textarea name="comments"></textarea>
                <button type="submit" id="add-to-cart-btn">Add to Cart</button>
            </form>

        </div>
        <?php
    }





}


?>