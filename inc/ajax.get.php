<?php
	session_start();
	
	//ini_set("display_errors", 1);
	//ini_set('log_errors', 1);
	//ini_set('error_log', dirname(__FILE__).'/logs/php_errors.txt');
	
        require_once  "../config.php";
        include_once  BASE_DIR."/inc/functions.php";
	function __autoload($class){
            $file = "../libs/".$class.".php";
                if(file_exists($file)) {
                    require_once $file;
                }
	}
        if(isset($_GET['price'])) $_GET['price'] = (float)str_replace(",", ".", $_GET['price']);
        if(isset($_GET['quantity_kg'])) $_GET['quantity_kg'] = (float)str_replace(",", ".", $_GET['quantity_kg']);
        
        
        require_once  BASE_DIR."/presenter/RecipePresenter.php";
        require_once  BASE_DIR."/presenter/OrderItemPresenter.php";
        require_once  BASE_DIR."/presenter/OrderRecipePresenter.php";
        require_once  BASE_DIR."/presenter/OrderByRecipePresenter.php";
        
        require_once  BASE_DIR."/service/ColorService.php";
        require_once  BASE_DIR."/service/RecipeService.php";
        require_once  BASE_DIR."/service/RecipeItemService.php";
        require_once  BASE_DIR."/service/OrderService.php";
        require_once  BASE_DIR."/service/OrderItemService.php";
        require_once  BASE_DIR."/service/OrderRecipeService.php";
        require_once  BASE_DIR."/service/OrderService.php";
        require_once  BASE_DIR."/service/OrderItemService.php";
        require_once  BASE_DIR."/service/CustomerService.php";

    
        
        $updateMsg = 'Zmeny boli úspešne uložené';
	$createMsg = 'Položka bola úspešne pridaná';
        $deleteMsg = 'Položka bola úspešne znazaná';
	
        $data = array( "err" => 1, "msg" => "Operáciu sa nepodarilo vykonať, skúste ju zopakovať" );
        try{
		$conn = Database::getInstance(SERVER, USER, PASS, DB_NAME);
                $auth = new Authenticate($conn);
                if(!$auth->isLogined()){ die(); }
		switch((int)$_GET['act']){
			/* Editacia pigmentov */
			case 1 : 
                            $cs = new ColorService($conn);
                            $cs->update($_GET['id'], $_GET['code'], $_GET['name'], $_GET['price'], $_GET['color_type'],  $_GET['id_measurement']);
                            $data = array( "err" => 0, "msg" => $updateMsg, "update" => 1 );
			break;
                    
                        /* Pridavanie pigmentov */
                         case 2 : 
                            $cs = new ColorService($conn);
                            $cs->create($_GET['code'], $_GET['name'], $_GET['price'], $_GET['color_type'],  $_GET['id_measurement']);
                            $data = array( "err" => 0, "msg" => $createMsg );
			break;
                    
                         /* Mazanie pigmentov */
                         case 3 : 
                            if($_GET['table'] == "product"){
                                $rs = new RecipeService($conn); 
                                $rs->delete($_GET['id']);
                            }elseif($_GET['table'] == "order"){
                                $os = new OrderService($conn);
                                $os->delete($_GET['id']);
                            }else{
                                $conn->delete("DELETE FROM `".$_GET['table']."` WHERE `id`=? LIMIT 1", array( $_GET['id']));
                            }
                            $data = array( "err" => 0, "msg" => $deleteMsg, "update" => 1 );
			break;
                    
                         case 4 :
                             if(Validator::isUsed($conn, "product", "label" , $_GET['label'])){
                             $data = array( "err" => 0, "msg" => "Položka so zadaným názvom sa už v databáze nachádza,
                                            napriek tomu ju chcete pridať?", "uniq" => 0 );
                             }else{
                                     $data = array( "err" => 0, "msg" => "", "uniq" => 1 );
                             }
                         break;

                         /* Pridanie novej receptury */
                         case 5 : 
                            $rs = new RecipeService($conn); 
                            $_GET['price'] = (!isset($_GET['price']) ? 0 : $_GET['price']); 
                            $rs->create($_GET['code'], $_GET['label'], $_GET['price'], $_GET['recipe']);
                            if($_GET['recipe'] == 1)
                                $data = array( "err" => 0, "msg" => "", "id" => $rs->getInsertId());
                            else
                                $data = array( "err" => 0, "msg" => $createMsg  );
			break;
                    
                        /* Upravenie receptúry (code, label, price) */
                         case 6 : 
                            $_GET['price'] = (!isset($_GET['price']) ? 0 : $_GET['price']); 
                            $rs = new RecipeService($conn);
                            $rs->update($_GET['code'], $_GET['label'], $_GET['price'], $_GET['id']);
                            $data = array( "err" => 0, "msg" => $updateMsg, "update" => 1  );
			break;
                    
                         /* Pridanie novej polozky receptury */
                         case 7 : 
                            $ris = new RecipeItemService($conn);
                            $ris->create($_GET['id'], $_GET['id_color'], $_GET['quantity_kg']);
                            $rp = new RecipePresenter($conn, WEIGHT_UNIT, PRICE_UNIT, null, $ris ); 
                            $data = array( "err" => 0, 
                                            "msg" => $createMsg, 
                                            "data" => $rp->getTbodyOfTableItems($_GET['id']),
                                            "totalPrice" => $rp->getResume() );
			break;
                    
                        /* EDITACIA polozky receptury */
                        case 8 : 
                            $ris = new RecipeItemService($conn);
                            $ris->update($_GET['quantity_kg'], $_GET['id']);
                            $rp = new RecipePresenter($conn, WEIGHT_UNIT, PRICE_UNIT, null, $ris ); 
                            $data = array( "err" => 0, 
                                            "msg" => $updateMsg, 
                                            "data" => $rp->getTbodyOfTableItems($_GET['recepeId']),
                                            "totalPrice" => $rp->getResume() );
			break;
                            /* MAZANIE polozky receptury */
                            case 9 : 
                            $ris = new RecipeItemService($conn);
                            $ris->delete($_GET['id']);
                            $rp = new RecipePresenter($conn, WEIGHT_UNIT, PRICE_UNIT, null, $ris ); 
                            $data = array( "err" => 0, 
                                            "msg" => $updateMsg, 
                                            "data" => $rp->getTbodyOfTableItems($_GET['recepeId']),
                                            "totalPrice" => $rp->getResume() );
			break;
                            
                            /* AUTOCOMPLETE customer */
                            case 10 : 
                            $data = $conn->select("SELECT * FROM `customer` WHERE `name` LIKE '%".$_GET["term"]."%' LIMIT 10");
                            if($data == null) $data = array();
                            echo  $_GET["cb"] . "(" . json_encode($data) . ")";  
                            exit;  
			break;
                                                    
                            /* Pridanie novej objednávky customer */
                            case 11 : 
                            $os = new OrderService($conn);
                            $os->create($_GET['id_customer'], $_GET['date'], $_GET['label']);
                            $data = array( "err" => 0, "msg" => "", "id" => $os->getInsertId()); 
			break;
                            /* AUTOCOMPLETE product */
                            case 12 : 
                            $data = $conn->simpleQuery("SELECT `id`, `code`, `label` FROM `product` WHERE `label` LIKE '". 
                                            $_GET["term"]."%' OR `code` LIKE '". $_GET["term"]."%' ORDER BY `code` LIMIT 12");
                            if($data == null) $data = array();
                            echo  $_GET["cb"] . "(" . json_encode($data) . ")";  
                            exit;
                            
			break;
                        /* Add new order item to order */
                            case 13 : 
                            $ois = new OrderItemService($conn);
                            $ois->create($_GET['id_order'], $_GET['id_product'], $_GET['quantity_kg'], $_GET['price_sale']);
                            $oip = new OrderItemPresenter($conn, WEIGHT_UNIT, PRICE_UNIT, null, $ois );
                            $data = array( "err" => 0, 
                                            "msg" => $createMsg, 
                                            "data" => $oip->getTbodyOfTableItems($_GET['id_order']),
                                            "totalPrice" => $oip->getResume(PRICE_UNIT));
                                break;
                         /* INLINE editing poctu ks v obejdnavke */
                            case 14 : 
                            $ois = new OrderItemService($conn);
                            $oip = new OrderItemPresenter($conn, WEIGHT_UNIT, PRICE_UNIT, null, $ois );
                            $ois->update($_GET['id'], $_GET['quantity'],$_GET['price_sale']);
                            $data = array( "err" => 0, 
                                            "msg" => $updateMsg, 
                                            "data" => $oip->getTbodyOfTableItems($_GET['orderId']),
                                            "totalPrice" => $oip->getResume(PRICE_UNIT));
			break;
                    
                          /* mazanie */
                            case 144 : 
                            $ois = new OrderItemService($conn);
                            $oip = new OrderItemPresenter($conn, WEIGHT_UNIT, PRICE_UNIT, null, $ois );
                            $ois->delete($_GET['id']);
                            $data = array( "err" => 0, 
                                            "msg" => $updateMsg, 
                                            "data" => $oip->getTbodyOfTableItems($_GET['orderId']),
                                           "totalPrice" => $oip->getResume(PRICE_UNIT));
			break;
                    
                            /* Upravenie poctu KG v recepture objednavky */
                         case 15 : 
                            $ris = new OrderRecipeService($conn);
                            $ris->updateItem($_GET['id'], $_GET['quantity'], $_GET['price_sale']);
                            $info = $ris->getRecipeInfo($_GET['id']);
                            $rp = new OrderRecipePresenter($conn, WEIGHT_UNIT, PRICE_UNIT, $ris ); 
                            $data = array( "err" => 0, 
                                            "data" => $rp->getTbodyOfTableItems($_GET['id'], $info[0]['id_order']),
                                            "totalPrice" => $rp->getResume(),
                                            "total" => $rp->getTotalWeight(),
                                            "totalWeight" => $rp->getWeight());
			break;
                    
                            /* inline editing v polozke objednavky */
                         case 16 : 
                            $ris = new OrderRecipeService($conn);
                            $ris->updateItemPriceAndQunatity($_GET['id'], $_GET['quantity_kg'], $_GET['price']);
                            $info = $ris->getRecipeInfo($_GET['recepeId']);
                            $rp = new OrderRecipePresenter($conn, WEIGHT_UNIT, PRICE_UNIT, $ris ); 
                            $data = array( "err" => 0, 
                                            "data" => $rp->getTbodyOfTableItems($_GET['recepeId'], $info[0]['id_order']),
                                            "totalPrice" => $rp->getResume(),
                                            "total" => $rp->getTotalWeight() );
			break;
                    
                    
                            /* Mazanie polozky receptury z OBjednavky */
                           case 17 : 
                            $ris = new OrderRecipeService($conn);
                            $ris->delete($_GET['id']);
                            $info = $ris->getRecipeInfo($_GET['recepeId']);
                            $rp = new OrderRecipePresenter($conn, WEIGHT_UNIT, PRICE_UNIT, $ris ); 
                            $data = array( "err" => 0, 
                                            "data" => $rp->getTbodyOfTableItems($_GET['recepeId'], $info[0]['id_order']),
                                            "totalPrice" => $rp->getResume(),
                                            "total" => $rp->getTotalWeight(),
                                            "totalWeight" => $rp->getWeight());
			break;
                    
                            /* Pridanie novej polozky receptury do obj. */
                           case 18 : 
                            $ors = new OrderRecipeService($conn);
                            $info = $ors->getRecipeInfo($_GET['id']);
                            if(isset($_GET['calculate']) && $_GET['calculate'] == "on"){
                                $os = new OrderItemService($conn);
                                $os->updateItemSalePrice($_GET['new_price_sale'], $_GET['id']);      
                            }
                            $ors->create($info[0]['id'] , $_GET['id_color'], $_GET['price'], $_GET['quantity_kg'], $_GET['idOrder']);
                            $orp = new OrderRecipePresenter($conn, WEIGHT_UNIT, PRICE_UNIT, $ors ); 
                            $data = array( "err" => 0, 
                                            "data" => $orp->getTbodyOfTableItems($_GET['id'], $_GET['idOrder']),
                                            "totalPrice" => $orp->getResume(),
                                            "total" => $orp->getTotalWeight(),
                                            "totalWeight" => $orp->getWeight());
                            if(isset($_GET['calculate']) && $_GET['calculate'] == "on")
                                                $data["price_sale"] = $_GET['new_price_sale'];
			break;
                           /* AUTOCOMPLETE customer */
                            case 19 : 
                            $data = $conn->select("SELECT `label` as name FROM `product` WHERE `supplier`= ".$_SESSION['supplier']." AND `label` LIKE '%".$_GET["term"]."%' LIMIT 10");
                            if($data == null) $data = array();
                            echo  $_GET["cb"] . "(" . json_encode($data) . ")";  
                            exit;  
			break;
                        
                         /* Pridanie noveho zakaznika */
                            case 20 : 
                            $cs = new CustomerService($conn);
                            $cs->create($_GET['name'], $_GET['street'], 
                                        $_GET['zip'], $_GET['city'], 
                                        $_GET['ico'], $_GET['dic'],
                                        $_GET['contact_person'], $_GET['email'], $_GET['tel']);    
                            $data = array( "err" => 0, "msg" => $createMsg );    
			break;
                            /* Pridanie noveho zakaznika */
                            case 21 : 
                            $cs = new CustomerService($conn);
                            $cs->update($_GET['name'], $_GET['street'], 
                                        $_GET['zip'], $_GET['city'], 
                                        $_GET['ico'], $_GET['dic'], $_GET['id'],
                                        $_GET['contact_person'], $_GET['email'], $_GET['tel']);    
                            $data = array( "err" => 0, "msg" => $updateMsg ,"update" => 1);    
			break;
                            
                        /* Aktualizcia poznamky k objednavke */
                        case 22 : 
                            $os = new OrderService($conn);
                            $os->updateLabel( $_GET['id'], $_GET['label']);
                            $data = array( "err" => 0, "msg" => $updateMsg ,"update" => 1);    
                        break;
                        /* Aktualizcia datumu objednavke */
                        case 23 : 
                            $os = new OrderService($conn);
                            $os->updateDate( $_GET['id'], $_GET['date']);
                            $data = array( "err" => 0, "msg" => $updateMsg ,"update" => 1);    
                        break;
                    
                        case 24 : 
                            $cs = new ColorService($conn);
                            $d = $cs->recievById($_GET['id']);
                            $data = array( "err" => 0, "price" => floatval($d[0]['price']), "unit" => $d[0]['unit'],"material_type" => $d[0]['color_type'] );    
                        break;
                        // zisti cenu produktu, na zakladne dnej sa pocita percentualny zisk
                        case 25 : 
                            $rs = new RecipeService($conn); 
                            $data = array( "err" => 0, "msg" => "", "price" => $rs->getProductPrice($_GET['id']));
                        break;
                       // skopiruje objednavku
                        case 26 : 
                            $os = new OrderService($conn);
                            $newOrderId = $os->copyOrder($_GET['orderId']);
                            $data = array( "err" => 0, "msg" => "", "newOrderId" => $newOrderId);
                        break;
                        // zobrazi v objednavke 5 posledny objednavok daneho produktu
                        case 27 : 
                            $orp = new OrderByRecipePresenter($conn, WEIGHT_UNIT, PRICE_UNIT);
                            $html = $orp->printOrderByProductAndCustomer($_GET['idProduct'], $_GET['idCustomer'], $_GET['idOrder']);
                            if(!$html) $html = 0;
                            $data = array( "err" => 0, "msg" => "", "html" => $html);
                        break;
                     
		}
        }catch(ValidationException $e){
                $data = array( "err" => 1, "msg" => $e->getMessage() );    
	}catch(MysqlException $e){
                $data = array( "err" => 1, "msg" => "Vyskytol sa problém s databázou, operáciu skúste zopakovať" );    
	}
        
        
       // if(isset($data['msg'])) $data['msg'] = utf8_encode($data['msg']);
        echo $_GET["cb"] . "(" .json_encode( $data ) . ")";
	exit;
        
        
