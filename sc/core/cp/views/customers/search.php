<div class="row">
    <div class="span5 offset6">
        <h1>Customer search</h1>
    </div>
</div>
<div id="customer_search" class="row">        
    <form action="<?=sc_cp('Customer/do_search')?>" class="form-horizontal" method="POST">
        <div class="span5 offset5">           
            <div class="control-group">
                <label class="control-label" for="custid">Customer Number</label>
                <div class="controls">                
                    <input type="text" id="custid" name="custid"/>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="email">Email</label>
                <div class="controls">
                    <input type="text" id="email" name="email" placeholder="example@domain.com"/>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="search_type">Search by</label>
                <div class="controls">
                    <select id="search_type" name="search_type">
                        <option name="ship_">Shipping address</option>
                        <option name="bill_">Billing address</option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="firstname">Name</label>
                <div class="controls">
                    <input type="text" id="firstname" name="firstname" placeholder="First" />
                    <input type="text" name="initial" placeholder="Middle Initial" maxlength="10" />
                    <input type="text" name="lastname" placeholder="Last" />                
                </div>
            </div>
        </div>
        <div class="span5">
            <div class="control-group">
                <label class="control-label" for="streetaddress">Address</label>
                <div class="controls">
                    <input type="text" id="streetaddress" name="streetaddress" placeholder="Street" />
                    <input type="text" name="apt" placeholder="Apartment #" />
                    <input type="text" name="city" placeholder="City" />                
                    <input type="text" name="city" placeholder="State" /> 
                    <input type="text" name="city" placeholder="Postalcode" /> 
                    <select name="country">
                        <option class="disabled">Country</option>
                        <?php include('core/includes/countrycodes.html') ?>
                    </select>
                </div>
            </div>
            
            <div class="control-group">
                <label class="control-label" for="phone">Phone #</label>
                <div class="controls">
                    <input type="text" id="phone" name="phone" />
                </div>
            <div>
        </div>
        
        <div class="control-group">
            <div class="controls">
                <input type="submit" value="Search" />
            </div>
        </div>
    </form>
</div></div><!--#customer_search-->
