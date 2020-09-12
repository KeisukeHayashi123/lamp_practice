<header>
  <nav class="navbar navbar-expand-sm navbar-light bg-light">
    //h関数追加
    <a class="navbar-brand" href="<?php print(HOME_URL);?>">Market</a>
    <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#headerNav" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="ナビゲーションの切替">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="headerNav">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item">
          //h関数追加
          <a class="nav-link" href="<?php print(SIGNUP_URL);?>">サインアップ</a>
        </li>
        <li class="nav-item">
          //h関数追加
          <a class="nav-link" href="<?php print(LOGIN_URL);?>">ログイン</a>
        </li>
      </ul>
    </div>
  </nav>
</header>
