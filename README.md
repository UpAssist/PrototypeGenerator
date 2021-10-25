# UpAssist.PrototypeGenerator

## What does this package do?

> This package lets you create scaffolding for prototypes for Neos using the commandline.

## Why did you make this?

Working with an Atomic setup in Neos often requires creating quite some files for a single NodeType.

Fusion files, CSS files and sometimes specific JS files as well.

While working with templates like textExpander or livetemplates in PhpStorm, still creating the files takes up some
time.

Not anymore! 😃

## ⚙️ How to install

Simply run the composer command to install the package.

```composer require upassist/prototypegenerator```

## How to setup

### 🚀 Quick Setup

Copy the following Settings and set your defaults:

```yaml
UpAssist:
  PrototypeGenerator:
    # Set a default packageKey and you don't have to write the full `Vendor.Package` prefix every time  (but you still can) (optional)
    packageKey: ''
    # The rendering prototypes
    # For document, content and collection you can list the additional prototypes to render
    rendering:
      document:
        - template
      content:
        - organism
        - molecule
        - atom
      collection:
        - organism
        - molecule
        - atom
    # List of types you also want to create a CSS file
    renderCSS: [ 'template', 'organism', 'molecule', 'atom' ]
    # List of types you also want to create a JS file
    renderJS: [ 'organism' ]
    # The file extension used for rendering the CSS file (i.e. `css`, `scss`, `sass`, `less`)
    cssExtension: 'css'
    # The file extension used for rendering the JS file (i.e. `js`, `jsx`)
    jsExtension: 'js'
    # The name for your extendedRenderer (optional, but required in the default setup since the default templates use this)
    extendedRenderer: ''
```

### 👉🏻 Advanced Setup

Take a look at all the Settings where you can set defaults for how you would like to render your prototypes, change the inheritance and more.

## 📘 How to use

There are a couple of commands at your disposal:

| Command | Params | Example |
| --- | --- | --- |
| `generator:prototype` | --nodeType (name of your nodetype, if your set your packageKey in the settings, you can omit it here) | `./flow generator:prototype Content.TextWithImage` |
| `generator:atom` | --prototypeName (name of your prototype, if your set your packageKey in the settings, you can omit it here) | `./flow generator:atom Text` |
| `generator:molecule` | --prototypeName (name of your prototype, if your set your packageKey in the settings, you can omit it here) | `./flow generator:molecule TextWithImage` |
| `generator:organism` | --prototypeName (name of your prototype, if your set your packageKey in the settings, you can omit it here) | `./flow generator:organism TextWithImage` |
| `generator:template` | --prototypeName (name of your prototype, if your set your packageKey in the settings, you can omit it here) | `./flow generator:template Article` |
| `generator:extendedrenderer` | --prototypeName (name of your prototype, if your set your packageKey in the settings, you can omit it here) | `./flow generator:extendedrenderer Vendor.Package:ExtendedRenderer` |

All commands support the `--force` parameter to overwrite files that are already created.

## 💡 Nice to know
- The default templates add a `@Todo` in the generated files (you can modify this in the Settings) allowing you to use your IDE to see what you still must implement, probably best to remove it after you are done.
- You can modify the default templates to your liking using sprintf syntax to substitute strings; the settings show you what you can use.
- You can modify one or multiple templates. Or none if you like the defaults.
- This is build with atomic fusion use in mind, but should be possible to be used without that.
- Did I mention the generator created the content prototype and all atomic parts as well if you want it to? 😎

## 🧠 Thought about but not yet implemented...
- Generate XLIFF files for all nodeTypes and defined language dimensions
- Implement more defaults for properties
- ...

## 🤔 Afterthought...
This really was not meant to be ready and could probably be better thought through. However it works pretty decent for the setup I work with and am very happy with the amount oof time saved using this tool.
I really hope it makes your Neos development life easier and welcome pull requests of course 😊

Thanks for reading this far and if you like it, let me know 🙂.

— _Henjo_
