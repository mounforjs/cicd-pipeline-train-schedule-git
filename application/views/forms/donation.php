<div class="container theme-background-white main-body">
  <div class="col-md-12">
    <h2 id="donationModalTitle" class="text-center p-2"></h2>
    <div class="row donate-bar">
      <div class="col-md-4 theme-blue">
        Available Credits: <p id="availCredits"><?php echo getBalance(); ?></p>
      </div>
      <div class="col-md-8">
      <span id="donateText"> Please select an amount to donate: </span>
        <ul class="nav navbar-nav navbar-left donate-buttons" id="donate-buttons" style="flex-direction: row;">
          <li><a href="#">
              <button class="btn-blue active" data-dollars='25' name="amount">
                $25
              </button>
            </a></li>
          <li><a href="#">
              <button class="btn-blue" data-dollars='50' name="amount">
                $50
              </button>
            </a></li>
          <li><a href="#">
              <button class="btn-blue" data-dollars='100' name="amount">
                $100
              </button>
            </a></li>
          <li><a href="#">
              <button class="btn-blue" data-dollars='500' name="amount">
                $500
              </button>
            </a></li>
          <li id="other"><a href="#">
              <button class="btn-blue-other" data-dollars='other'>
                OTHER
              </button>
            </a></li>
          <li id="other-input">
            <span>$</span>
            <input type="number" name="amount" class="amount" id="dAmount" />
          </li>
          <label class="checkbox-inline disclaimerCheck">
          <span id="donationNote">Note: You will be charged the donation amount (<span id='noteAmount'></span>) + 1% convenience fee (<span id='noteAmountFee'></span>)</span></br>
          <div class="mt-2">
          <span id="donationTotaltext">Total Charges: </span><span id="donationTotalCharges"></span></br>
          <span id="amountErrorCheck" class='text-danger errorCheck'></span></br>
          </div>
          <input type="checkbox" id="disclaimerCheckBox"> 
            <span id="disclaimer">I agree to donate to WinWinLabs on my behalf to support Beneficiary.</br>
              <span id = 'disclaimerErrorCheck' class='text-danger errorCheck'></span></br>
            </span>
          </label>
          <button type="button" class="btn red w-50 rounded-0 p-1 donationBtn">
            <i class="fas fa-donate"></i> DONATE <span id="donationAmount"></span></button>
        </ul>
      </div>
    </div>
    <!--/.donate-bar-->
  </div><!-- /.col-md-12 -->
</div>