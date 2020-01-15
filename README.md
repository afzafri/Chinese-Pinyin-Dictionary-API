# Chinese Pinyin Dictionary API
(Unofficial) Chinese English Pinyin Dictionary PHP API. Data are scraped from <a href="https://chinese.yabla.com/">Yabla</a>

The API will fetch:
- Both Simplified and Traditional Chinese Characters
- Pinyin
- MP3 Audio for pronunciation sample
- Meanings list

## Usage
- API Endpoint: http://pinyin.test/api.php?define=TEXT , where ```TEXT``` is your text to search (English, Pinyin or Chinese characters)
- By default, the API will fetch the first 50 records. To fetch all records, append '&records=NUMBER' to the endpoint , where ```NUMBER``` is your number of records to display
- Example: http://pinyin.test/api.php?define=wo&records=100, to fetch 100 records definitions of "wo"
- Sample output (JSON):
  ```
  {
      "simplified_char": "卧",
      "traditional_char": "臥",
      "audio": "https://chinese.yabla.com/audio/363462.mp3",
      "pinyin": "wò",
      "meanings": [
          "to lie",
          "to crouch"
      ]
  }
  ```
