<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>凝萃文旅</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Microsoft JhengHei', Arial, sans-serif;
        }

        body {
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }

        .header {
            position: relative;
            height: 300px;
            overflow: hidden;
        }

        .header-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .header-title {
            position: absolute;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            color: white;
            font-size: 3rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            background-color: rgba(0, 0, 0, 0.5);
            padding: 10px 0;
        }

        .login-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color:rgb(102, 38, 161);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1.2rem;
            transition: background-color 0.3s;
        }

        .login-btn:hover {
            background-color: rgb(25, 116, 177);
        }

        .content {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .section {
            margin-bottom: 40px;
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .section h2 {
            color: #4CAF50;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 15px 0;
            margin-top: 30px;
        }

        /* 統一圖片縮小尺寸 */
        img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
        }

        /* 房型圖片四張並排 */
        .room-images {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .room-images img {
            flex: 1 1 calc(25% - 10px);
            max-width: calc(25% - 10px);
            height: 120px; /* 可依需求調整 */
            object-fit: cover;
        }

        /* 休閒設施圖片縮小 */
        .facility {
            display: flex;
            align-items: flex-start;
            gap: 20px;
            flex-wrap: wrap;
        }

        .facility img {
            width: 200px;
            height: 150px;
            object-fit: cover;
            border-radius: 5px;
        }

        .facility-text {
            flex: 1; /* 文字佔據剩下空間 */
            min-width: 250px; /* 避免文字太窄 */
        }

        .facility-text p {
            line-height: 1.6;
        }

    </style>
</head>
<body>
    <div class="header">
        <img src="https://images.ezhotel.com.tw/nch_hotel/newspic/infopic/1_1_1.jpg" alt="凝萃文旅" class="header-image">
        <h1 class="header-title">凝萃文旅</h1>
        <a href="login.php" class="login-btn">登入</a>
    </div>

    <div class="content">
        <div class="section">
            <h2>飯店簡介</h2>
            <p>整體採用電影院風格設計，搭配房內寬敞明亮的空間，讓旅人感受當時懷舊的電影院情懷，又能好好放鬆。如果想規劃一趟城市度假輕旅行，來凝萃文旅就對了；
            來台中「凝萃文旅」一定是您最佳的選擇，響應環保救地球，房間不含一次性備品(梳子、刮鬍刀、浴帽、香皂、棉花棒、牙線棒)，僅提供壁掛式洗沐用品及毛巾、浴巾。感謝您為永續而做的每一步努力，致力推動更美好的世界。
            <br>
            飯店電話：06-2835961<br>
            飯店地址：台南市東區勝利路777號<br>
            入住時間：17:00之後<br>
            退房時間：11:00之前<br>
            飯店登記號：台南市旅館389號
            </p>
        </div>

        <!-- 客房介紹 -->
        <div class="section">
            <h2>客房介紹</h2>
            <p>多樣房型不論商務出差、家族旅遊或購物玩樂，皆可滿足不同客群的需求，凝萃文旅用心打造舒適空間，提供完善且乾淨舒服的環境，獨特電影風格，融入現代美學，讓旅客沉浸於靜謐愜意，享受悠悅的舒適空間，讓一夜好眠褪去一身疲憊的好地方。</p>
            <br>
            <div class="room-images">
                <img src="https://images.ezhotel.com.tw/nch_hotel/newspic/infopic/1_2_5.jpg" alt="房型1">
                <img src="https://images.ezhotel.com.tw/nch_hotel/newspic/infopic/1_2_1.jpg" alt="房型2">
                <img src="https://images.ezhotel.com.tw/nch_hotel/newspic/infopic/1_2_2.jpg" alt="房型3">
                <img src="https://images.ezhotel.com.tw/nch_hotel/newspic/infopic/1_2_3.jpg" alt="房型4">
            </div>
        </div>

        <!-- 休閒設施 -->
        <div class="section">
            <h2>休閒設施</h2>
            <div class="facility">
                <img src="https://im.marieclaire.com.tw/m800c533h100b0/assets/mc/202108/61139B229B5C01628674850.jpeg" alt="休閒設施">
                <div class="facility-text">
                    <p>
                        飯店內設有游泳池、SPA以及
                        <br><br>
                        【咖啡廳】挑高設計，大面積的大廳，配上復古電影院風格裝潢，不會感到壓迫很寬敞舒適，咖啡跟輕食都有一定的水準，值得歡迎大家來細細品味。
                        <br><br>
                        【健身房】健身房10:00-22:00 讓身體轉換能量，大口吸吐讓心跳加速，汗水落下時，穿上無形的自信。
                        <br><br>
                        【網美牆】凝萃文旅裡有超棒超大的拍照空間。櫃檯正前方有一面打卡牆，等候的時間拍拍照剛剛好。
                        <br><br>
                        【公共大廳】凝萃大廳有提供寄放行李服務(貴重物品記得隨身攜帶，我們不負責保管行李服務)，免費提供自助點心區可自行享用餅乾、茶水。
                    </p>
                </div>
            </div>
        </div>

    </div>
</body>
</html>
