<div class="ads">
    <?= $settings['dashboard_top_ad'] ?>
</div>
<?php
if (isset($_SESSION['message'])) {
    echo $_SESSION['message'];
} ?>
<?php
if (isset($message)) {
    echo $message;
} ?>
<div class="alert alert-danger text-center">SPAMMING DEPOSIT FORM WILL LEAD TO IMMEDIATE SUSPENSION OF YOUR ACCOUNT!</div>
<div class="row">
    <?php if ($settings['coinbase_deposit_status'] == 'on') { ?>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4 text-center">CoinBase Deposit</h4>
                    <form action="<?= site_url('/deposit/coinbase') ?>" method="POST" target="_blank" autocomplete="off">
                        <div class="form-group">
                            <label>Amount (USD) :</label>
                            <input type="number" name="amount" class="form-control" min="<?= $settings['minimum_deposit'] ?>" step="0.000001">
                        </div>
                        <input type="hidden" name="<?= $csrf_name ?>" value="<?= $csrf_hash ?>">
                        <div class="mt-4">
                            <button type="submit" class="btn btn-success">Deposit</button>
                        </div>
                        <small>You can pay with other currencies after creating the deposit.</small>
                    </form>
                    <div class="ads">
                        <?= $settings['dashboard_header_ad'] ?>
                    </div>
                </div>
            </div>
        </div>
    <?php }
    if ($settings['faucetpay_deposit_status'] == 'on') { ?>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4 text-center">FaucetPay Deposit</h4>
                    <form action="https://faucetpay.io/merchant/webscr" method="POST" target="_blank" autocomplete="off">
                        <div class="form-group">
                            <label>Amount (USD) :</label>
                            <input type="number" name="amount1" class="form-control" min="<?= $settings['minimum_deposit'] ?>" step="0.000001">
                        </div>

                        <?php if ($settings['faucetpay_currency'] == '') { ?>
                            <input type="hidden" name="currency2" value="<?= $settings['faucetpay_currency'] ?>">
                        <?php } else { ?>
                            <div class="form-group">
                                <label for="option">Currency</label>
                                <select class="form-control" id="currency2" name="currency2">
                                    <?php foreach ($faucetpayMethods as $method) { ?>
                                        <option value="<?= $method ?>"><?= $method ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        <?php } ?>
                        <input type="hidden" name="merchant_username" value="<?= $settings['faucetpay_username'] ?>">
                        <input type="hidden" name="item_description" value="Deposit to <?= $settings['name'] ?>">
                        <input type="hidden" name="currency1" value="USD">
                        <input type="hidden" name="custom" value="<?= $user['id'] ?>">
                        <input type="hidden" name="callback_url" value="<?= site_url('wh/faucetpay') ?>">
                        <input type="hidden" name="success_url" value="<?= site_url('deposit?success=true') ?>">
                        <input type="hidden" name="cancel_url" value="<?= site_url('deposit?success=false') ?>">
                        <div class="mt-4">
                            <button type="submit" class="btn btn-success">Deposit</button>
                        </div>
                        <small>You can pay with other currencies after creating the deposit.</small>
                    </form>
                    <div class="ads">
                        <?= $settings['dashboard_header_ad'] ?>
                    </div>
                </div>
            </div>
        </div>
    <?php }
    if ($settings['payeer_status'] == 'on') { ?>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4 text-center">Payeer Deposit</h4>
                    <form action="<?= site_url('/deposit/payeer') ?>" method="POST" autocomplete="off">
                        <div class="form-group">
                            <label>Amount (USD) :</label>
                            <input type="number" name="amount" class="form-control" min="<?= $settings['minimum_deposit'] ?>" step="0.001">
                        </div>
                        <input type="hidden" name="<?= $csrf_name ?>" value="<?= $csrf_hash ?>">
                        <div class="mt-4">
                            <button type="submit" class="btn btn-success">Deposit</button>
                        </div>
                    </form>
                    <div class="ads">
                        <?= $settings['dashboard_header_ad'] ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body text-center">
                <h4 class="card-title mb-4">Deposit history</h4>
                <?php
                if (isset($_SESSION['message'])) {
                    echo $_SESSION['message'];
                } ?>
                <div class="table-responsive">
                    <table class="table table-striped text-center">
                        <thead>
                            <tr>
                                <th scope="col">Code</th>
                                <th scope="col">Status</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($deposits as $deposit) {
                                if ($deposit['type'] == 1) {
                                    echo '<tr><td scope="row">Faucetpay: ' . $deposit["code"] . '</td><td>' . $deposit["status"] . '</td><td>' . $deposit["amount"] . ' USD</td><td>' . timespan($deposit["create_time"], time(), 2) . ' ago</td></tr>';
                                } else if ($deposit['type'] == 2) {
                                    echo '<tr><td scope="row">Coinbase: <a target="_blank" href="https://commerce.coinbase.com/charges/' . $deposit["code"] . '">' . $deposit["code"] . '</a></td><td>' . $deposit["status"] . '</td><td>' . $deposit["amount"] . ' USD</td><td>' . timespan($deposit["create_time"], time(), 2) . ' ago</td></tr>';
                                } else {
                                    echo '<tr><td scope="row">Payeer: ' . $deposit["code"] . '</td><td>' . $deposit["status"] . '</td><td>' . $deposit["amount"] . ' USD</td><td>' . timespan($deposit["create_time"], time(), 2) . ' ago</td></tr>';
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>