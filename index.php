<?php
require_once('scripts/classPage.php');
$thispage = new page();

$thispage->addStyleSheet('style.css');

//Slideshow


$footer='Copyright &copy;'.date('Y').' Aeroview. Design by <a href="http://kleingraphics.net" title="Klein Graphics">Klein Graphics</a>';

$thispage->title='Aeroview';

$flashinclude=include "itemflash.php";

$thispage->body.='
                  <div id="container">
                  <div id="header">
                       <img src="images/aeroviewback1.jpg" id="headerimage1">
                       <img src="images/aeroviewback2.jpg" id="headerimage2">
                  </div>
                  <div id="content">
                       <div id="customizetext" class="standardtext">
                            <h2 id="toptitle">Customize your Road Glide&reg; (RG)</h2>
                            <p>AeroView Motorsports offers one-of-a-kind custom
                            mirror mount Cowlings that feature the option of LED lights
                            and a mounted digital video camera.  We also carry custom
                            bag accessories and speaker bag covers.</p>
                       </div>
                       <div id="aboutbox" class="smalltext">
                         <img src="images/aboutus.jpg" id="aboutpic">
                         <p>There are millions of two-wheel Road Glide (RG) and Bagger enthusiasts globally!  
                         Each one has a unique style whether it is custom, stock, modified, chromed-out and even "bobbed" to the hilt.  
                         Even so, there is one thing that we all hold in common -- we want quality products that separate us from the 
                         pack and give us our individual freedom and style at a reasonable price.  AVM offers innovative and unique
                         products that enhance your riding experience.</p>
                         <p>AeroView Motorsports is a grassroots American company.  Our developers have backgrounds in aircraft-quality 
                         workmanship and military precision. We use our own products - testing them relentlessly, making improvements. It
                         is important to us that all of our products are of the highest quality and made in America.</p>
                         <p>Thank you for choosing AVM products and services. We know you will be pleased with our customer service and timely delivery.</p>
                         <p>AeroView Motorsports<br />
                         4120 Douglas Blvd  #306-122<br />
                         Granite Bay, Ca 95746<br />
                         916-765-3685</p>
                       </div>
                       <div id="navmenu"><ul><!--<li><a href="#">Videos</a></li>--><li><a href="contactform.html?ajax=true&width=400&height=200" rel="prettyPhoto" title="">Contact</a><a href="http://www.facebook.com/AeroViewMotorsports" title="Find Us On Facebook!"><img src="images/facebook.png" alt="Facebook"></a></li></ul></div>
                       <div id="simplegallery1"></div>
                       '.$flashinclude.'                       
                       <div id="storedesc" class="standardtext"><p>
                       AeroView products are quality designed for fun and to give you the ability to customize. 
                       We have taken great care to provide you with innovative and unique products - tested on our own machines 
                       - that will withstand the road whether you are haulin\' or ballin\'.  
                       Order from the store below, or contact us at <a href="mailto:info@aeroviewmotorsports.com">info@aeroviewmotorsports.com</a> or call us at 916-765-3685 with questions.
                       </p></div>
                       <div id="store" class="standardtext">
                            <div id="storehead">
                                 <img src="images/AeroviewStore.jpg" id="storeimage">[[ajax_loader]]
                                 <div id="cart">
                                      <div id="carthead" class="smallcaps"><div id="carttitle">Shopping Cart</div><div id="cartinfo">[[cartinfo]]</div></div>
                                      <div id="cartbuttons">[[viewcart]][[clearcart]]<span class="right">[[checkout]]</span></div>
                                 </div>
                                 <div class="push"></div>
                            </div>
                            [[display_cart]]
                            [[checkout_area]]
                            <div id="storebody">
                                 <div id="customerControl">[[custcontrol]]</div>
                                 <div class="storeitemwrapper">
                                      <div class="storeitem image">
                                      <a href="images/aerocowlingsBIG.jpg" rel="prettyPhoto" title="Cowlings"><img src="images/aerocowlingsthumb.jpg"></a>
                                      </div>
                                      <div class="storeitem desc">
                                      <h2>[[name|1]]</h2>
                                      <p>[[desc|1]]</p>
                                      </div>
                                      <div class="storeitem cartoptions">
                                      [[addtocart|1]]
                                      </div>
                                      <div class="push"></div>
                                 </div>
                                 
                                 <div class="storeitemwrapper">
                                      <div class="storeitem image">
                                      <a href="images/itemmirrorreloLARGE.jpg" rel="prettyPhoto" title="Relo kit"><img src="images/itemmirrorrelo.jpg"></a>
                                      </div>
                                      <div class="storeitem desc">
                                      <h2>[[name|2]]</h2>
                                      <p>[[desc|2]]</p>
                                      </div>
                                      <div class="altimages">
                                      <a href="images\itemmirrorreloLARGE2.jpg" rel="prettyPhoto" title="Relo kit">Detail View</a>
                                      </div>
                                      <div class="storeitem cartoptions">
                                      [[addtocart|2]]
                                      </div>
                                      <div class="push"></div>
                                 </div>
                                 
                                 <div class="storeitemwrapper">
                                      <div class="storeitem image">
                                      <a href="images/itembabsLARGE.jpg" rel="prettyPhoto" title="AeroView Bags"><img src="images/itembags.jpg"></a>
                                      </div>
                                      <div class="storeitem desc">
                                      <h2>[[name|i--AEROBAG]]</h2>
                                      <p>[[detail|description|i--AEROBAG]]
                                      </p>
                                      </div>
                                      <div class="altimages">
                                      <a href="images\bagliner1.jpg" rel="prettyPhoto" title="Bagger Liner">Detail View</a>
                                      <a href="images\bagliner2.jpg" rel="prettyPhoto" title="Bagger Lid Liner">Lid View</a>
                                      </div>
                                      <div class="storeitem cartoptions">
                                      [[addtocart|i--AEROBAG]]
                                      </div>
                                      <div class="push"></div>
                                 </div>
                                 <div class="storeitemwrapper">
                                      <div class="storeitem image">
                                      <a href="images/itemspeakersLARGE.jpg" rel="prettyPhoto" title="AeroView Speaker Lid Cover"><img src="images/itemspeakers.jpg"></a>
                                      </div>
                                      <div class="storeitem desc">
                                      <h2>[[name|6]]</h2>
                                      <p>[[desc|6]]
                                      </p>
                                      </div>
                                      <div class="storeitem cartoptions">
                                      [[addtocart|6]]
                                      </div>
                                      <div class="push"></div>
                                 </div>


                            </div>


                       </div>
                       <div class="push"></div>

                  </div>
                  <div class="push"></div>
                  <div id="footer">'.$footer.'</div>
                  </div>

';
require 'sc/core/page_in.php';
$thispage->renderPage(); 
require 'sc/core/page_out.php'; 
?>

