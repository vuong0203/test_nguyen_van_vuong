# laravel_jarvis

## Introduction

Build jarvis from laravel with docker-compose

## Usage

laravel_jarvis では Docker の立ち上げ、コマンド実行、Laravel プロジェクトの作成などの多くの操作を C 言語等の煩雑なビルド作業をまとめて 1 つのコマンドで実行できるようにするのに広く使われている make で実行できるように[Makefile](https://github.com/valleyin-dev/laravel_jarvis/blob/main/Makefile)を用意しています。

公式サイト( https://www.gnu.org/software/make/ )から make コマンドを実行する環境をインストールして使用すると楽になるかもしれません。

このリポジトリでは既に Laravel のプロジェクトは作成されてしまっているので必要なライブラリ等だけ composer の install でインストールすれば大丈夫なはずです。

make コマンドがインストールされている場合は以下のコマンドを実行して頂ければ、現在リポジトリに作成されているプロジェクトが localhost で立ち上がるまで完了すると思います。

```bash
$ make init
```

http://localhost

また初めに、既存のマイグレーションファイルを実行するために

```bash
make refresh
```

を実行してください。既にプロジェクトの方で作成されているテーブル構造にローカルのデータベースを統一することができます。

## Tips

Read this Wiki(hove not implemented). <!-- [Wiki](). -->

## Container structure

```bash
├── app
├── web
└── db
```

### app container

- Base image
  - [php](https://hub.docker.com/_/php):7.4-fpm-buster
  - [composer](https://hub.docker.com/_/composer):2.0

### web container

- Base image
  - [nginx](https://hub.docker.com/_/nginx):1.18-alpine
  - [node](https://hub.docker.com/_/node):14.2-alpine

### db container

- Base image
  - [mysql](https://hub.docker.com/_/mysql):8.0

#### Persistent MySQL Storage

By default, the [named volume](https://docs.docker.com/compose/compose-file/#volumes) is mounted, so MySQL data remains even if the container is destroyed.
If you want to delete MySQL data intentionally, execute the following command.

```bash
$ docker-compose down -v && docker-compose up
```

### コミュニケーション

- Slack にファンリターンのワークスペースがあり、質問共有チャンネルでバグ報告や開発においての質問などをする。
- 通知専用チャンネルは本番環境のサーバーエラー通知と GitHub の通知などが送られる。

### ClickUp

Using ClickUp to manage project
See more detail at valleyin wiki

## 開発に使用するもの

- ER図<br>
こちらにテーブルの説明やカラムの内容等を記載しています。ER図の変更があれば下記の、更新方法を参考に更新してください。<br>
https://ondras.zarovi.cz/sql/demo/?keyword=fanreturn
<details>
<summary>更新方法</summary>

1. 画面右上の「SAVE / LOAD」をクリック<br>
<img width="192" src="https://user-images.githubusercontent.com/66456130/160050433-f5d515a7-7fc0-40ac-8fe8-556c41cb59ac.png"><br>
2. 下記画像の「SAVE」ボタンをクリック<br>
<img width="387" src="https://user-images.githubusercontent.com/66456130/160050437-83e56341-eb5d-4456-9932-ff4bc6bbfc81.png"><br>
3. 「OK」ボタンをクリック<br>
<img width="445" src="https://user-images.githubusercontent.com/66456130/160050439-a207ff5c-4c9e-472f-a803-a518d789f260.png">
</details>


- メール<br>
ローカル環境でメールの動作を確認する際はこちらのURLでMailLogにアクセスしてください。<br>
http://localhost:8025

- クレジットカードのテスト番号<br>
https://resource-sharing.co.jp/ec-sites-credit-card-test-number/

- GMO PAYMENTの決済と送金サービスの仕様書（実装の際に参照ください）<br>
<details>
<summary>各種仕様書</summary>

【決済サービス】<br>
決済機能の実装や修正の際に参照ください。OrderID、ShopID、JobCd等のパラメータの説明も記載されています。<br>
[800_クレジットカード決済利用マニュアル_1.17.pdf](https://github.com/valleyin-dev/fan-return-laravel/files/8347530/800_._1.17.pdf)<br>

【送金サービス】<br>
銀行口座登録や送金処理の実装や修正の際に参照ください。Deposit_ID、Bank_ID、その他銀行のパラメータの説明も記載されています。<br>
[【GMO-PG送金サービス】(A2)API仕様書-銀行振込編_20210824.pdf](https://github.com/valleyin-dev/fan-return-laravel/files/8347529/GMO-PG.A2.API.-._20210824.pdf)<br>

【トークン仕様書】<br>
gmo-create-card-token.jsファイルの決済に使用するトークン生成処理を修正する際に参照ください。
[トークン決済サービス仕様書_1_33.pdf](https://github.com/valleyin-dev/fan-return-laravel/files/8347531/_1_33.pdf)<br>
</details>

# 仕様

__最終更新日 : 2022年3月5日__

## 目次
- [概要](#概要)
- [ユーザーの仕様](#ユーザーの仕様)
- [インフルエンサーの仕様](#インフルエンサーの仕様)
- [管理者の仕様](#管理者の仕様)
- [通知の仕様](#通知の仕様)

## 概要

<details>
<summary>本文を展開</summary>

## __【ユーザー画面】__<br>
支援募集者（インフルエンサーやライバー等）がライブやイベント、やりたい事を叶える為にファンから資金を募ります。<br>
支援募集者はクラファンプロジェクトを作成し、支援したユーザーに対して返礼品や何らかのお礼をリターンとして設定します。<br>
プロジェクトを作成したら、管理者に申請を出し、審査にて問題なければ一定期間掲載されます。<br>
プロジェクトの募集形式はAll or Nothing か All inを採用しています。<br>
※詳細な仕様は下記 __All or Nothing と All in方式__ で解説しています。<br>

## __【管理画面】__<br>
プロジェクト募集者が作成したプロジェクトの審査や管理、資金の送金等を行います。<br>
プロジェクト、リターン、活動報告、支援者、ユーザーや管理者同士のメッセージ等を管理（CRUD処理）できます。<br>
※詳細な仕様は下記 __ユーザーの仕様__ や __管理者の仕様__ で説明しています。<br>

## __【PS（プロジェクトサポーター）リターン】__<br>
- リターンの中にはプロジェクトサポーター（以下「PS」という）リターンという特別なリターンがあります。<br>
これはプロジェクトを支援したユーザーが他のユーザーにそのプロジェクトを紹介、支援します。<br>
そして、プロジェクトを紹介した数をランキングで競い合い、そのランキングに応じてPSリターンの報酬を受け取る事ができます。
- PSはプロジェクトの紹介URLをSNSや知人で紹介し、紹介を受けたユーザーがそのリンクを踏んでから支援をした場合、ランキングの紹介人数に加算されます。<br>
__※開発当初は紹介したユーザーの支援総額で競い合う支援総額順のランキングも存在しましたが、現在は一旦保留との事でコメントアウトしています。（2022/3/5時点）__
- ランキングの何位までが報酬を受け取れるか、リターン内容等はプロジェクト実行者に委ねられています。

## __【All or Nothing と All in方式】__<br>
- All or Nothing<br>
予め設定した期間内に目標金額を達成することで、プロジェクト実行者は期間終了日までに集まった支援額を獲得できます。<br>
期限以内（50日以下）に目標金額に達成しなかった場合はプロジェクトは不成立となり、支援金はユーザーに返金されます。
※現在は50日ですが、客先の要望で変わる可能性があります。（2022/3/5時点）
- All in<br>
目標金額に達成しなかったとしても、プロジェクト実行者は期間終了日までに集まった応援購入額を獲得できます。

## __【決済機能について】__<br>
現在は決済機能としてGMO PAYMENTを実装しています。<br>
GMO PAYMENTを採用している理由は、All or Nothing方式でクラファンプロジェクトが目標金額に達しなかった場合、手数料無料で返金できるからです。（最長60日まで）<br>
GMO PAYMENTではクレジット決済日から最長60日まで「仮売上」として決済を計上できます。「仮売上」中は手数料無料で返金することができます。<br>
 「All or Nothing方式で目標金額に達成する」及び「All in方式」でプロジェクトが掲載終了となった場合にのみ、「本売上」として計上します。一度「本売上」にしてしまうと、決済を取り消す場合、手数料分は戻ってきません。

***
</details>



## ユーザーの仕様

<details>
<summary>本文を展開</summary>

### 新規会員登録画面

- OAuth（SNSやGoogle認証）で新規会員登録をする際、SNSのメールアドレスの共有設定が「無効」となっている場合、SNSでの会員登録は出来ません。<br>
SNSのメールアドレスの共有設定を「有効」にしてから、登録してください。

### クラファンTOP画面
<details>
<summary>本文を展開</summary>

- ヘッダー部の「エントリー一覧」は人気クリエーターとのコラボに募集する為のページに遷移します。<br>
__※こちらはWordPressにて作成されており、弊社の方で各種修正に対応しています。__
- ヘッダー部の「プロジェクト一覧」はクラファンのページに遷移します。<br>
__※こちらはLaravelにて作成されており、フリーランスや業務委託の方に対応して頂く部分になります。__
- TOP画面最上部にあるLINEの友達追加部分は、プロジェクトを立ち上げたいインフルエンサーが気軽に運営と相談出来る様に設置されています。<br>
<img width="667" src="https://user-images.githubusercontent.com/66456130/159869917-f031f667-2870-4daa-b14f-bbaf79fd71e0.png">

- TOP画面の一番上に掲載されているプロジェクトはランダムで表示しています。
- 「ランキング」は支援者数順が多いプロジェクト順に並んでいます。また、「現在の支援者数」は同じユーザーが何度購入しても購入した回数分が人数としてカウントされます。<br>
※以前「現在の支援者数」は同じユーザーが複数回購入しても、支援者数を1人としてカウントしていました。<br>
そのロジックはProject.phpファイルのscopeGetWithPaymentsCountAndSumPriceメソッドにコメントアウトで残しています。（2022/3/24時点）
- 「新規プロジェクト」はプロジェクト開始日順に並んでいます。
- 「掲載終了プロジェクト」は2022年1月6日以前のプロジェクトは非表示としています。（客先より、ベータ版の時に作成したプロジェクトを一旦非表示にして欲しいとの要望があった）
- プロジェクトの達成率(目標額に対する支援総額)は、30%以下,30%,50%,90%,100%以上の5段階で色が変わっていきます。<br>
以前、All-In方式でプロジェクトの達成率に応じて、リターンの報酬内容を変えるとのことでした。しかし現状はその様な仕様ではない為、元の達成率の表示のみに戻す可能性はあります。（2022/3/24時点）
<img width="993" src="https://user-images.githubusercontent.com/66456130/159861200-c99e9539-0c5f-48ef-aa1b-cfa8538133d3.png">

- 「もっと見る」ボタンからプロジェクト検索画面に遷移出来ます。<br>
※以前はヘッダーに検索アイコンがあり、プロジェクト検索画面に遷移できましたが、ヘッダーの項目が増えて、現状は削除しています。<br>
今後掲載数が増えると検索機能を使用する頻度も増える為、再度検索アイコンを設置する可能性があります。（2022/3/5時点）
- カテゴリごとに検索も可能です。検索したいカテゴリをクリックしてください。<br>
<img width="891" src="https://user-images.githubusercontent.com/66456130/159858162-e24b4b52-8a5e-4442-9d50-443615fc71ac.png"><br>

- 下記画像の「よくある質問・ヘルプ」は未実装です。（2022/3/24時点）<br>
<img width="372" src="https://user-images.githubusercontent.com/66456130/159857557-c04ef6c8-cccd-4c7f-8557-3d42868b4822.png">

- 現状の仕様ではあまり使用されませんが、フッターにお問い合わせフォームがあります。
</details>

### プロジェクト検索画面

- プロジェクトのワード検索、ソート、絞り込みが可能です。
- 並び替えの「人気順」は現在「お気に入り数」の多い順にソートしています。今後修正の可能性はあります。（2022/3/5時点）

### プロジェクト詳細画面

<details>
<summary>本文を展開</summary>

- クラファンプロジェクトの内容、画像、動画、目標金額、終了日、リターン等の様々な情報を閲覧できます。
- 活動報告はプロジェクト募集者がプロジェクト進捗を投稿、発信する目的で使用します。<br>
※プロジェクトを支援したユーザーのみプロジェクトの「活動報告」を閲覧できます。
- 応援コメントはプロジェクト募集者に向けて応援メッセージを投稿する目的で使用します。<br>
※全てのユーザーが応援コメントを投稿出来ます。今後支援者しか投稿出来ないように仕様変更となる可能性もあります。
- プロジェクト支援後にPS解説画面（PSになる画面）やPSランキング画面に遷移する為のボタンが表示されるようになります。<br>
<img width="513" src="https://user-images.githubusercontent.com/66456130/160031551-efd62791-6ad4-481a-9994-52d04b7980c6.png"><br>
<img width="531" src="https://user-images.githubusercontent.com/66456130/160031558-b54d7634-e9dd-424e-80c1-8d268279f580.png">
</details>

### プロジェクト決済画面

<details>
<summary>本文を展開</summary>

- 購入に際し、ユーザー情報を入力します。リターンは各種複数購入が可能です。
- クレジット決済とコンビニ決済をが可能です。
- __開発初期に「Pay.JP」と「PayPay」で決済処理を実装していましたが、クライアントの要望により、「stripe」に変更しました。__<br>
__しかしその後、決済を仮売上からキャンセルできる期間が長い決済代行サービスに変更したいとの要望があり、最終的には「GMO PAYMENT」で実装しています。念の為、Pay.JP, PayPay, Stripeの処理は残しています。__
- クレジットカードのテストを実施したい場合は以下のサイトを参考にしてください。<br>
https://resource-sharing.co.jp/ec-sites-credit-card-test-number/
- コンビニ決済は、決済後に表示される「受付番号」と「確認番号」を用いて、支払い期限内（5日間）に支払いを行います。<br>
決済完了画面だけでなく、下記画像の通り購入履歴の画面からも確認できます。
<img width="785" src="https://user-images.githubusercontent.com/66456130/160036554-f69e7d1f-e424-4bfb-90dd-daf4cb345bd1.png">
</details>

### PS解説画面（PSになる画面）

- プロジェクトを支援したユーザーのみ訪れる事が可能で、PSについての説明やPSになる為の招待リンクボタンがあります。
- __開発当初、PSと一般のユーザーで権限やできる事を分けたいとの要望があり、当ページに訪れたプロジェクト支援者を保存するPSテーブル（user_project_supported）を作成しました。現在の仕様では特段使用する事は無いですが、将来使用する可能性もある為、テーブルは残しています。（2022/3/5時点）__

### PSリターンランキング画面

- プロジェクトを支援したユーザーのみ訪れる事が可能です。PSとしてプロジェクトを紹介し、紹介したユーザーがプロジェクトを支援した人数のランキング（支援者数順）があります。<br>
__※概要のPSリターンで解説した通り、支援総額順のランキングはコメントアウト中。projectsテーブルのreward_by_total_amountカラムにあたります。カラムは残したままとしています。（2022/3/5時点）__

### プロフィール画面

- インフルエンサーの「出身地」は敢えて自由入力ができる入力フォームにしています。インフルエンサーが面白い出身地やネタとして書いてもいい様にする為です。（客先要望）
- OAuth（SNSやGoogle認証）でログインした場合はプロフィールにメールアドレスが表示されません。

### 購入履歴 / PSになる 画面

- 購入したリターンの詳細が記載された履歴を確認できます。また「PSになる」、「PSランキングページ」へ遷移できます。
- オーダーIDは管理画面の「支援者(ファン)管理」で検索すると、該当する購入履歴が参照できます。また、そのIDを用いてGMO PAYMENTのダッシュボードで購入履歴を確認できます。

### DM画面

- メッセージの送り先として、「ユーザーと運営」、「ユーザー → インフルエンサー（プロジェクト実行者）」、「インフルエンサー（プロジェクト実行者） → ユーザー」の3種類があります。
- メッセージ未読件数が下記のように表示されます。
<img width="888" src="https://user-images.githubusercontent.com/66456130/160033003-1b5c3c43-d050-456b-addd-de6eb52ad5ba.png">


***

</details>



## インフルエンサーの仕様

<details>
<summary>本文を展開</summary>

### マイプロジェクト一覧画面

- プロジェクトを作成し、資金を募りたいユーザーが使用するページです。下書き中のプロジェクトや掲載中のプロジェクトなどが一覧表示されています。プロジェクト作成後は管理者（ファンリターン運営会社）へ審査してもらう為に申請する事ができます。
- プロジェクトの審査を申請する際、入力に不備があればアラートで表示されます。アラートで表示された箇所を修正すると、申請可能です。
- 対象のプロジェクトから編集、詳細画面に遷移できます。
- プロジェクトのステータスは以下の通りです。<br>
<details>
<summary>本文を展開</summary>

  - 【下書き中】<br>
    プロジェクトを作成して、申請していない状態。<br>
    <img width="322" src="https://user-images.githubusercontent.com/66456130/159872730-1f447851-3cc1-4a55-b1fd-54b9cea00341.png">
  - 【承認待ち】<br>
    プロジェクト申請し、承認されていない状態。<br>
    <img width="302" src="https://user-images.githubusercontent.com/66456130/159872733-fb14ec0b-b66d-4b0f-b29c-d9f187928b58.png">
  - 【差し戻し】<br>
    プロジェクトを申請したが、修正箇所がある為、再度編集と申請が必要。<br>
    <img width="316" src="https://user-images.githubusercontent.com/66456130/159872739-99984086-6f98-410a-90e1-ddbb2191eb3a.png">
  - 【公開前、公開中、公開終了】<br>
    プロジェクトを申請後、掲載許可が降りた状態。掲載開始日になると自動で「公開中」となり、終了すると「公開終了」に切り替わる。<br>
    __※管理画面はこのステータスではなく、一律で「掲載中」のステータスとなっている。__<br>
    __掲載開始日から終了日のプロジェクトのみ、TOP画面やプロジェクト検索画面に表示される。（2022/3/5時点）__<br>
    <img width="303" src="https://user-images.githubusercontent.com/66456130/159872723-c9988dc0-314d-4e0c-a12b-829560e7c69b.png">
  - 【掲載停止中】<br>
    プロジェクト募集者が何らかの理由でプロジェクトを継続できなくなった、または不適切なユーザーであった場合に緊急で使用します。<br>
    この状態はプロジェクトの公開が取り消され、編集、詳細の閲覧ができなくなります。<br>
    <img width="303" src="https://user-images.githubusercontent.com/66456130/159872737-ad432c51-13f9-4100-b8a2-e7dfbf2f18b3.png">
</details>

### マイプロジェクト編集画面

- クラファンで支援者を募る為にプロジェクトを作成、編集ができるページです。作成したプロジェクトはプレビューで確認する事ができます。
- フォームに入力すると非同期で保存されます。
- 各タブの仕様や注意点を以下の通りです。<br>
<br>
【目標設定】<br>

  - 掲載開始日を選択すると、掲載終了日は最大で50日までしか選択出来ません。
  - 掲載開始日は明日以降の日付を選択可能です。<br>
【概要】<br>
  - 概要文はリッチエディタで、画像や動画も挿入することができます。<br>
【Top画像】<br>
  - 動画は1つだけ登録可能で、プロジェクト詳細のスライダー画像集の一番最初に表示されます。短縮URLも登録可能です。<br>
【リターン】<br>
  - 「限定数」はグッズ等のリターンで個数の上限が必要になる場合に設定します。<br>
  - 「お届け予定日」はプロジェクト終了月の翌月から選択可能です。
  - 「住所情報の取得」はリターンにTシャツやグッズ等が含まれる場合、支援したユーザーにグッズを発送する際に住所が必要となります。その場合はチェックを入れます。<br>
【PSリターン】<br>
  - こちらの画面でプレビューを確認すると、PSランキングの画面が表示されます。<br>
【本人確認】<br>
  - 銀行口座の入力フォームは別のページにある為、そちらで入力が必要です。<br>

### マイプロジェクト詳細画面

<details>
<summary>本文を展開</summary>

- プロジェクトの掲載ステータスによって扱える機能が異なります。<br>
【掲載中】<br>
<img width="409" src="https://user-images.githubusercontent.com/66456130/159873969-68b78626-8534-468c-bd0b-81d7a61a54ac.png"><br>
【下書き中、承認待ち、差し戻し、掲載停止中】<br>
<img width="405" src="https://user-images.githubusercontent.com/66456130/159873921-91592a04-c3eb-41c0-ba5c-b50859f5deed.png"><br>
</details>

### 支援者一覧画面
<details>
<summary>各種処理状況</summary>

- プロジェクトを支援したユーザーにグッズ等を贈る必要がある時、支援者の住所情報や処理状況（決済状況）を閲覧できます。
- グッズ等を発送したユーザーをメモする時にも使えます。例えばグッズを発送したユーザーはステータスを「発送済」にできます。<br>
__また、処理状況（決済状況）が「実売上」と「決済完了(コンビニ決済)」の時に「発送済」に変更し、発送してください。__<br>
<img width="654" src="https://user-images.githubusercontent.com/66456130/160064089-4345dd89-c191-486c-83bc-d64fb2623a07.png">

- 各種処理状況（決済状況）は以下の通りです。<br>
  - 仮売上<br>
  クレジットカードにて決済は済んでいるが、まだ「実売上」となっていない状態。プロジェクトが終了し、「実売上」となればグッズを発送する。<br>
  - 実売上<br>
  クレジットカードにて決済が済んでおり、グッズを送信しても良い状態。<br>
  <!-- - キャンセル(取消)<br>
  後で高木さんに確認<br>
  - キャンセル(返品)<br>
  後で高木さんに確認<br>
  - キャンセル(月跨り返品)<br>
  後で高木さんに確認<br> -->
  - 要求成功(コンビニ決済)<br>
  FanReturnにてコンビニ決済処理が完了しているが、実際にコンビニにて支払っていない状態。<br>
  - 決済完了(コンビニ決済)<br>
  FanReturnにてコンビニ決済処理が完了し、コンビニにて支払いが完了している状態。<br>
  - 期限切れ(コンビニ決済)<br>
  FanReturnにてコンビニ決済処理が完了しているが、5日以内にコンビニに支払いをしていない状態。<br>
  <!-- - 支払停止(コンビニ決済)<br>
  後で高木さんに確認<br> -->
  </details>


### 支援者とのDM

- インフルエンサー → 支援者のDMができます。もし、何らかの連絡（支援者が引っ越し予定で住所が変わってしまう等）が必要な場合は個別やりとりします。

### コメント一覧

- 支援者からの応援コメントに返信できます。また、自由に削除もできます。

### 活動報告一覧

- 支援者へ向けて、プロジェクトの進捗を発信します。活動報告の作成、編集、削除ができます。


### 銀行口座登録画面

- こちらで登録した銀行口座情報はプロジェクトで調達した資金をインフルエンサーに振り込む際に使用します。<br>
また、登録した銀行口座情報はGMO PAYMENT側に保存されます。
  

***
</details>



## 管理者の仕様

<details>
<summary>本文を展開</summary>

### プロジェクト管理画面

- ここではクラファンプロジェクトの閲覧、作成、編集、削除が可能です。そのほかにもプロジェクトの審査や掲載のステータス変更、プロジェクトの送金処理等を行います。<br>
  ※1 送金処理の方法については下記の __プロジェクト完了後の送金の流れ__ を参照下さい。<br>
  ※2 掲載ステータスについては __ユーザーの仕様__ 内にある __マイプロジェクト一覧画面__ を参照下さい。
- プロジェクトに関するリターン、活動報告、応援コメント、支援者管理も可能です。
- 「キュレーター」とは管理側のプロジェクト担当者です。プロジェクトの審査や送金、やりとり等を行う役割があります。

### プロジェクト完了後の送金の流れ

こちらを読む前に __概要__ の __決済機能について__ を参照願います。<br>


<details>
<summary>All or Nothing方式で目標金額達成後 もしくは All in方式でプロジェクト期間終了後の送金処理</summary>

1. プロジェクト終了→管理者に通知メール→通知メールのリンクをクリック→対象の「プロジェクト管理」画面に遷移する
   もしくは「プロジェクト管理」画面にて終了したプロジェクトを検索する<br>
![Image](https://user-images.githubusercontent.com/66456130/156914492-9907a607-a831-454b-b0e2-e721b8b8baa8.png)<br>

2. 画面右端にある「支援者（ファン）一覧」ボタンから「支援者（ファン）管理」画面へ<br>
![Image2](https://user-images.githubusercontent.com/66456130/156914493-c409090a-7315-4bb2-ae3a-c182dcfc8875.png)<br>

3. 上部にある「処理状況」のセレクトボックスを「仮売上」にすると、仮売上中の支払い状態で絞り込まれる

4. 「実売上計上」ボタンをクリックすると、支払いのステータスが「仮売上」→「実売上」に変化する<br>
![Image](https://user-images.githubusercontent.com/66456130/156880532-e2bc3ac1-fc2d-4622-9b70-54ccf15eaccf.png)<br>
※1 プロジェクトが掲載期間が終了していないにも関わらず、実売上に変更した場合以下のエラーメッセージが表示されます。<br>
![Image](https://user-images.githubusercontent.com/66456130/156914395-c73a49b6-f693-48fb-97e9-2a024e077a0f.png)<br>

※2 処理状況を「仮売上」に絞り込まずに「実売上計上」ボタンを押すと、下記のエラーが表示されます。<br>
<img width="353" src="https://user-images.githubusercontent.com/66456130/160041533-66936eee-90ae-4320-b630-d964ea6fcee5.png"><br>

※3 目標金額に達していない場合、下記画像の通り「実売上計上」のボタンが表示されません。<br>
<img width="1393" src="https://user-images.githubusercontent.com/66456130/160041343-b3f1fc91-d587-40dc-8eb5-8c4a23bc6ab9.png">

5. プロジェクトIDが記載されているボタンをクリックし、先程の「プロジェクト管理」画面に戻る<br>
![Image](https://user-images.githubusercontent.com/66456130/156880867-3277fcf5-296e-46a9-a076-5ea5d6d5b396.png)<br>

6. 画面中央あたりに位置する「プロジェクト経費」を入力し、更新する<br>
![Image](https://user-images.githubusercontent.com/66456130/156881193-d71512d6-5ae6-484f-aeed-c893a5420218.png)<br>

7. 「送金実行する」ボタンにて、クラファンプロジェクト実行者に「プロジェクト経費」と「手数料(FR売上)」を差し引いた「合計支払い金額」が振り込まれる<br>
![Image](https://user-images.githubusercontent.com/66456130/156881234-749853dc-2d67-4577-9e9a-6c3d7cec5365.png)<br>
※1 プロジェクト実行者が銀行口座情報を入力していない場合、以下の様に表示されます<br>
![Image](https://user-images.githubusercontent.com/66456130/156914025-3d3a1f0f-bd2e-4cac-84c8-af4eae433fc6.png)<br>
※2 「仮売上」の決済が残っている場合、以下のエラーが表示されます。<br>
<img width="519" src="https://user-images.githubusercontent.com/66456130/160041984-6ca28467-d0d6-46e7-94e1-ac539a9368e8.png">
</details>


<details>
<summary>All or Nothing方式で目標金額未達成 もしくは 何らかの理由でプロジェクトを終了後の返金処理</summary>

1. 上記の1〜3までは同様の流れ

2. 「売上キャンセル」（画面右端）ボタンをクリックすると、支払いのステータスが「仮売上」→「キャンセル」に変化する<br>
![Image](https://user-images.githubusercontent.com/66456130/156913925-40b78a0a-8ba4-482c-9d3b-88d7136506e2.png)
</details>

### その他決済関連の仕様
<details>
<summary>本文を展開</summary>

- __GMO PAYMENTのダッシュボードで「仮売上」「実売上」「キャンセル（取消）」等に変更可能ですが、FanReturn側でエラーが起きるので、極力管理画面にて操作してください。__
- 「売上キャンセル」ボタンは「All-or-Nothing」で目標金額未達成時や何らかの理由でユーザーが決済をキャンセルしたい場合に用います。
- 「仮売上」、「実売上」中に「キャンセル」する場合は決済日から180日以内まで可能です。<br>
<img width="705" src="https://user-images.githubusercontent.com/66456130/160039499-ecb1056c-d20e-46df-a5bd-5bf6586702e1.png">

- クレジットカードの打ち間違いや予審枠が足りなかった場合等に決済が失敗します。その際は下記画像の通り「支援者（ファン）管理」画面の「処理状況」で「決済失敗」のステータスとなります。<br>
<img width="1200" src="https://user-images.githubusercontent.com/66456130/160040628-a699ac15-5df3-482e-820a-95a8ab9b3a52.png">
</details>

### リターン管理、支援者（ファン）管理、活動報告管理、コメント管理画面
<details>
<summary>本文を展開</summary>

- これら（下記画像参照）は全プロジェクトのリターンや活動報告等々が一括で閲覧できますが、基本的にあまり使用されません。<br>
<img width="206" src="https://user-images.githubusercontent.com/66456130/160042664-8414a685-f65f-4e2a-8a2e-acd59deaf7af.png"><br>
なるべくこれら（下記画像参照）の各プロジェクトからアクセスしてください。<br>
<img width="130" src="https://user-images.githubusercontent.com/66456130/160042668-78fb7c35-c301-4683-a27c-244844fa25c6.png">
</details>

### DM一覧画面

- ユーザーとのDMが可能です。
- 未読のメッセージ件数が下記の通りに表示されます。<br>
<img width="795" src="https://user-images.githubusercontent.com/66456130/160032994-a96ccc01-dad7-45ff-8e7e-5a72df008727.png">

### ユーザー管理画面

- ユーザーのCRUD処理が可能です。

### キュレーター管理画面

- キュレーターのCRUD処理が可能です。
- キュレーターとは管理側のプロジェクト担当者です。プロジェクトの審査や送金、やりとり等を行う役割があります。

### タグ管理画面

- プロジェクトに添付するタグのCRUD処理が可能です。

### 各種設定画面

- 管理画面のadminの名前、メールアドレス、パスワードを変更できます。

***
</details>


## 通知の仕様

<details>
<summary>本文を展開</summary>

<img width="1355" src="https://user-images.githubusercontent.com/66456130/160034911-15f8d6ee-92fc-4daf-9cd7-6ea607da9f6f.png">
<img width="1364" src="https://user-images.githubusercontent.com/66456130/160035012-67257c49-d8d9-4218-af2b-91d5b0c812ee.png">
<img width="1364" src="https://user-images.githubusercontent.com/66456130/160035109-bb5a7ed0-430c-45e7-9602-1a8bc5add32e.png">
<img width="1362" src="https://user-images.githubusercontent.com/66456130/160035218-50b46845-5c21-48a6-8b2d-118742b228c2.png">
<img width="1363" src="https://user-images.githubusercontent.com/66456130/160035329-35125b1e-e7fa-424b-8309-167bc2080d41.png">
<img width="1364" src="https://user-images.githubusercontent.com/66456130/160035345-c10a4d64-4131-400d-8de6-f04d5f0d922c.png">
<img width="1365" src="https://user-images.githubusercontent.com/66456130/160035354-76d64d8c-33c9-49f4-b607-402a25df7352.png">
<img width="1365" src="https://user-images.githubusercontent.com/66456130/160035359-500ed17e-318c-4f24-9f59-8290b1d719e6.png">
<img width="1361" src="https://user-images.githubusercontent.com/66456130/160035362-5b10e5f1-d22d-4405-9fe5-de082bb43360.png">

***
</details>