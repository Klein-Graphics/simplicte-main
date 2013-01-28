<form class="form-horizontal"> 
    <div class="control-group">
        <label class="control-label" for="email">Email</label>
        <div class="controls">
            <input type="text" name="email" id="email" class="input-medium">
        </div>
    </div>
    <fieldset id="shipping-info">
        <legend>Shipping Information</legend>
        <div class="control-group">
            <label class="control-label" for="ship_firstname">Name</label>        
            <div class="controls">
                <input type="text" placeholder="First" name="ship_firstname"    id="ship_firstname" class="input-small">
                <input type="text" placeholder="M I"   name="ship_initial"      id="ship_initial"   class="input-mini">
                <input type="text" placeholder="Last"  name="ship_lastnamename" id="ship_lastname"  class="input-small">                                             
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="ship_address">Address</label>
            <div class="controls">
                <input type="text" placeholder="Street Address" name="ship_address" id="ship_address" class="input-medium">
                <input type="text" placeholder="Apt#"           name="ship_apt"     id="ship_apt"     class="input-mini">                
            </div>                        
            <div class="controls">
                <input type="text" placeholder="City"        name="ship_city"       id="ship_city"       class="input-small">
                <input type="text" placeholder="State"       name="ship_state"      id="ship_state"      class="input-small">
                <input type="text" placeholder="Postal Code" name="ship_postalcode" id="ship_postalcode" class="input-small">
            </div>
            <div class="controls">
                <select name="ship_country" id="ship_country">
                    <option disabled="disabled" selected="selected">Country</option>
                    <?php include 'core/includes/countrycodes.html'?>
                </select>               
            </div>                
        </div>
        <div class="control-group">
            <label class="control-label" for="ship_phone">Phone</label>
            <div class="controls">
                <input type="text" name="ship_phone" id="ship_phone" class="input-medium">
            </div>
        </div>
    </fieldset>
    <fieldset id="shipping-info">
        <legend>Billing Information</legend>
        <div class="control-group">
            <label class="control-label" for="bill_firstname">Name</label>        
            <div class="controls">
                <input type="text" placeholder="First" name="bill_firstname"    id="bill_firstname" class="input-small">
                <input type="text" placeholder="M I"   name="bill_initial"      id="bill_initial"   class="input-mini">
                <input type="text" placeholder="Last"  name="bill_lastnamename" id="bill_lastname"  class="input-small">                                             
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="bill_address">Address</label>
            <div class="controls">
                <input type="text" placeholder="Street Address" name="bill_address" id="bill_address" class="input-medium">
                <input type="text" placeholder="Apt#"           name="bill_apt"     id="bill_apt"     class="input-mini">                
            </div>                        
            <div class="controls">
                <input type="text" placeholder="City"        name="bill_city"       id="bill_city"       class="input-small">
                <input type="text" placeholder="State"       name="bill_state"      id="bill_state"      class="input-small">
                <input type="text" placeholder="Postal Code" name="bill_postalcode" id="bill_postalcode" class="input-small">
            </div>
            <div class="controls">
                <select name="bill_country" id="bill_country">
                    <option disabled="disabled" selected="selected">Country</option>
                    <?php include 'core/includes/countrycodes.html'?>
                </select>               
            </div>                
        </div>
        <div class="control-group">
            <label class="control-label" for="bill_phone">Phone</label>
            <div class="controls">
                <input type="text" name="bill_phone" id="bill_phone" class="input-medium">
            </div>
        </div>
    </fieldset>
</form>
