# TTF to Code

This tool converts True Type Fonts to code structures. Only C for arduino implemented yet (With PROGMEM). Feel free to contribute.

Example : 

```php
$ttftocode = new TtfToCode();

$ttftocode->setFontSize(12);
$ttftocode->setFontFile("/path/to/font.ttf");
$ttftocode->setCharRange(33, 126); // visible ascii chars
$ttftocode->setBitsPerPixel(4);
$ttftocode->setLanguage('c1');		

$result = $ttftocode->process();
``` 

This will output something like :
```c
// font height = 13
// font bpp = 4
// characters = 94
// ASCII offset = 33

const struct font_char_t arial_regular_12pt_atlas [94] PROGMEM = 
{
    /* ! */ { 2, 8, 2, 0, 4 },
    /* " */ { 3, 3, 2, 4, 3 },
    /* # */ { 7, 8, 2, 7, 14 },
    /* $ */ { 6, 12, 0, 21, 18 },
    /* % */ { 8, 8, 2, 39, 16 },
    /* & */ { 7, 8, 2, 55, 14 },
    /* ' */ { 2, 2, 2, 69, 1 },
    /* ( */ { 4, 12, 1, 70, 12 },
    /* ) */ { 4, 12, 1, 82, 12 },
    /* * */ { 5, 5, 2, 94, 7 },
    [...]
      /* { */ { 4, 11, 1, 958, 11 },
    /* | */ { 2, 9, 2, 969, 5 },
    /* } */ { 4, 11, 1, 974, 11 },
    /* ~ */ { 7, 2, 6, 985, 4 }
};

const char arial_regular_12pt_bitmap[] PROGMEM = {0x33, 0x33, 0x32, 0x03, [...] 0x29, 0x8b, 0xc0};

```
