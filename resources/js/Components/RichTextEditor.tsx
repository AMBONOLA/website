import { Popover, PopoverButton, PopoverPanel } from '@headlessui/react';
import TextAlign from '@tiptap/extension-text-align';
import {
    BackgroundColor,
    Color,
    FontFamily,
    FontSize,
    TextStyle,
} from '@tiptap/extension-text-style';
import {
    Editor,
    EditorContent,
    Extension,
    useEditor,
    useEditorState,
} from '@tiptap/react';
import StarterKit from '@tiptap/starter-kit';
import { ReactNode } from 'react';

// The server sanitizer (Insight::sanitizeHtml) only keeps the tags and CSS properties this editor produces.
// Avoid `ui-*` font keywords: the sanitizer quotes them, which turns them into unknown font names.
const fontFamilies = [
    { label: 'Default font', value: '' },
    { label: 'Serif', value: 'Georgia, serif' },
    { label: 'Times', value: '"Times New Roman", Times, serif' },
    { label: 'Sans', value: 'Arial, Helvetica, sans-serif' },
    { label: 'Monospace', value: 'Menlo, Consolas, "Courier New", monospace' },
];

const fontSizes = ['', '14px', '16px', '20px', '24px', '30px', '36px'];

const textColors = [
    { label: 'Stone', value: '#57534e' },
    { label: 'Mustard', value: '#b07a0d' },
    { label: 'Red', value: '#dc2626' },
    { label: 'Green', value: '#16a34a' },
    { label: 'Blue', value: '#2563eb' },
    { label: 'Purple', value: '#9333ea' },
];

const highlightColors = [
    { label: 'Yellow', value: '#faf0c4' },
    { label: 'Green', value: '#dcfce7' },
    { label: 'Blue', value: '#dbeafe' },
    { label: 'Pink', value: '#fce7f3' },
    { label: 'Orange', value: '#ffedd5' },
    { label: 'Grey', value: '#e7e5e4' },
];

const normalizeFontFamily = (value: string): string =>
    value
        .replace(/['"]/g, '')
        .replace(/\s*,\s*/g, ',')
        .trim();

function setLink(editor: Editor): void {
    const previous = editor.getAttributes('link').href ?? 'https://';
    const url = window.prompt(
        'Link URL (leave empty to remove the link)',
        previous,
    );

    if (url === null) {
        return;
    }

    if (url.trim() === '') {
        editor.chain().focus().extendMarkRange('link').unsetLink().run();

        return;
    }

    if (editor.state.selection.empty && !editor.isActive('link')) {
        editor
            .chain()
            .focus()
            .insertContent({
                type: 'text',
                text: url,
                marks: [{ type: 'link', attrs: { href: url } }],
            })
            .run();

        return;
    }

    editor.chain().focus().extendMarkRange('link').setLink({ href: url }).run();
}

const LinkShortcut = Extension.create({
    name: 'linkShortcut',

    addKeyboardShortcuts() {
        return {
            'Mod-k': () => {
                setLink(this.editor);

                return true;
            },
        };
    },
});

function AlignIcon({
    align,
}: {
    align: 'left' | 'center' | 'right' | 'justify';
}) {
    const lines = {
        left: ['M3 6h18', 'M3 10h12', 'M3 14h18', 'M3 18h12'],
        center: ['M3 6h18', 'M6 10h12', 'M3 14h18', 'M6 18h12'],
        right: ['M3 6h18', 'M9 10h12', 'M3 14h18', 'M9 18h12'],
        justify: ['M3 6h18', 'M3 10h18', 'M3 14h18', 'M3 18h18'],
    }[align];

    return (
        <svg
            viewBox="0 0 24 24"
            className="h-4 w-4"
            fill="none"
            stroke="currentColor"
            strokeWidth={2}
            strokeLinecap="round"
            aria-hidden="true"
        >
            {lines.map((d) => (
                <path key={d} d={d} />
            ))}
        </svg>
    );
}

type ToolbarButton = {
    label: ReactNode;
    title: string;
    run: (editor: Editor) => void;
    isActive?: (editor: Editor) => boolean;
    isDisabled?: (editor: Editor) => boolean;
};

const button = (item: ToolbarButton): ToolbarButton => item;

const formatButtons = [
    button({
        label: <span className="font-bold">B</span>,
        title: 'Bold (Ctrl+B)',
        run: (editor) => editor.chain().focus().toggleBold().run(),
        isActive: (editor) => editor.isActive('bold'),
    }),
    button({
        label: <span className="italic">I</span>,
        title: 'Italic (Ctrl+I)',
        run: (editor) => editor.chain().focus().toggleItalic().run(),
        isActive: (editor) => editor.isActive('italic'),
    }),
    button({
        label: <span className="underline">U</span>,
        title: 'Underline (Ctrl+U)',
        run: (editor) => editor.chain().focus().toggleUnderline().run(),
        isActive: (editor) => editor.isActive('underline'),
    }),
    button({
        label: <span className="line-through">S</span>,
        title: 'Strikethrough (Ctrl+Shift+S)',
        run: (editor) => editor.chain().focus().toggleStrike().run(),
        isActive: (editor) => editor.isActive('strike'),
    }),
    button({
        label: 'Link',
        title: 'Link (Ctrl+K)',
        run: setLink,
        isActive: (editor) => editor.isActive('link'),
    }),
];

const blockButtons = [
    ...([2, 3, 4] as const).map((level) =>
        button({
            label: `H${level}`,
            title: `Heading ${level}`,
            run: (editor) =>
                editor.chain().focus().toggleHeading({ level }).run(),
            isActive: (editor) => editor.isActive('heading', { level }),
        }),
    ),
    button({
        label: '• List',
        title: 'Bulleted list',
        run: (editor) => editor.chain().focus().toggleBulletList().run(),
        isActive: (editor) => editor.isActive('bulletList'),
    }),
    button({
        label: '1. List',
        title: 'Numbered list',
        run: (editor) => editor.chain().focus().toggleOrderedList().run(),
        isActive: (editor) => editor.isActive('orderedList'),
    }),
    button({
        label: 'Quote',
        title: 'Quote',
        run: (editor) => editor.chain().focus().toggleBlockquote().run(),
        isActive: (editor) => editor.isActive('blockquote'),
    }),
    button({
        label: 'Code',
        title: 'Inline code (Ctrl+E)',
        run: (editor) => editor.chain().focus().toggleCode().run(),
        isActive: (editor) => editor.isActive('code'),
    }),
    button({
        label: 'Code block',
        title: 'Code block (Ctrl+Alt+C)',
        run: (editor) => editor.chain().focus().toggleCodeBlock().run(),
        isActive: (editor) => editor.isActive('codeBlock'),
    }),
    button({
        label: '―',
        title: 'Divider line',
        run: (editor) => editor.chain().focus().setHorizontalRule().run(),
    }),
];

const alignButtons = (['left', 'center', 'right', 'justify'] as const).map(
    (align) =>
        button({
            label: <AlignIcon align={align} />,
            title: `Align ${align}`,
            run: (editor) => editor.chain().focus().setTextAlign(align).run(),
            isActive: (editor) => editor.isActive({ textAlign: align }),
        }),
);

const historyButtons = [
    button({
        label: 'Undo',
        title: 'Undo (Ctrl+Z)',
        run: (editor) => editor.chain().focus().undo().run(),
        isDisabled: (editor) => !editor.can().undo(),
    }),
    button({
        label: 'Redo',
        title: 'Redo (Ctrl+Shift+Z)',
        run: (editor) => editor.chain().focus().redo().run(),
        isDisabled: (editor) => !editor.can().redo(),
    }),
    button({
        label: 'Clear',
        title: 'Clear formatting',
        run: (editor) =>
            editor.chain().focus().unsetAllMarks().clearNodes().run(),
    }),
];

const allButtons = [
    ...formatButtons,
    ...blockButtons,
    ...alignButtons,
    ...historyButtons,
];

const buttonClassName = (isActive: boolean) =>
    'inline-flex h-8 min-w-8 items-center justify-center rounded px-2 text-sm font-medium transition disabled:cursor-not-allowed disabled:opacity-40 ' +
    (isActive
        ? 'bg-mustard-100 text-mustard-900'
        : 'text-stone-600 hover:bg-stone-200 hover:text-stone-900');

const selectClassName =
    'h-8 rounded border-stone-300 py-0 pl-2 pr-7 text-sm text-stone-700 focus:border-mustard-500 focus:ring-mustard-500';

function Divider() {
    return <span className="mx-1 h-5 w-px bg-stone-300" aria-hidden="true" />;
}

function ColorPicker({
    title,
    icon,
    current,
    colors,
    onPick,
    onClear,
}: {
    title: string;
    icon: ReactNode;
    current: string | null;
    colors: { label: string; value: string }[];
    onPick: (color: string) => void;
    onClear: () => void;
}) {
    return (
        <Popover>
            <PopoverButton
                title={title}
                aria-label={title}
                className={buttonClassName(false) + ' flex-col gap-0.5 py-1'}
            >
                {icon}
                <span
                    className="h-1 w-4 rounded-full border border-stone-300"
                    style={{ backgroundColor: current ?? 'transparent' }}
                />
            </PopoverButton>
            <PopoverPanel
                anchor="bottom start"
                className="z-50 mt-1 w-48 rounded-md border border-stone-200 bg-white p-2 shadow-lg"
            >
                {({ close }) => (
                    <>
                        <div className="grid grid-cols-6 gap-1.5">
                            {colors.map((color) => (
                                <button
                                    key={color.value}
                                    type="button"
                                    title={color.label}
                                    aria-label={color.label}
                                    className="h-6 w-6 rounded border border-stone-300 hover:scale-110"
                                    style={{ backgroundColor: color.value }}
                                    onClick={() => {
                                        onPick(color.value);
                                        close();
                                    }}
                                />
                            ))}
                        </div>
                        <div className="mt-2 flex items-center justify-between gap-2 border-t border-stone-100 pt-2 text-sm">
                            <label className="flex cursor-pointer items-center gap-2 text-stone-600 hover:text-stone-900">
                                <input
                                    type="color"
                                    className="h-6 w-6 cursor-pointer rounded border-0 p-0"
                                    onChange={(e) => onPick(e.target.value)}
                                />
                                Custom
                            </label>
                            <button
                                type="button"
                                className="text-stone-500 hover:text-stone-900"
                                onClick={() => {
                                    onClear();
                                    close();
                                }}
                            >
                                Clear
                            </button>
                        </div>
                    </>
                )}
            </PopoverPanel>
        </Popover>
    );
}

export default function RichTextEditor({
    value,
    onChange,
    labelledBy,
}: {
    value: string;
    onChange: (value: string) => void;
    labelledBy: string;
}) {
    const editor = useEditor({
        extensions: [
            StarterKit.configure({
                heading: { levels: [2, 3, 4] },
                link: { openOnClick: false, defaultProtocol: 'https' },
            }),
            TextStyle,
            FontFamily,
            FontSize,
            Color,
            BackgroundColor,
            TextAlign.configure({ types: ['heading', 'paragraph'] }),
            LinkShortcut,
        ],
        content: value,
        editorProps: {
            attributes: {
                'aria-labelledby': labelledBy,
                class: 'insight-body min-h-[30rem] px-6 py-5 focus:outline-none',
            },
        },
        onUpdate: ({ editor }) =>
            onChange(editor.isEmpty ? '' : editor.getHTML()),
    });

    // Only re-render the toolbar when something it displays actually changes.
    const state = useEditorState({
        editor,
        selector: ({ editor }) => {
            const textStyle = editor?.getAttributes('textStyle') ?? {};

            return {
                buttons: allButtons.map((item) => ({
                    isActive: editor
                        ? (item.isActive?.(editor) ?? false)
                        : false,
                    isDisabled: editor
                        ? (item.isDisabled?.(editor) ?? false)
                        : true,
                })),
                fontFamily: normalizeFontFamily(textStyle.fontFamily ?? ''),
                fontSize: (textStyle.fontSize as string | undefined) ?? '',
                color: (textStyle.color as string | undefined) ?? null,
                backgroundColor:
                    (textStyle.backgroundColor as string | undefined) ?? null,
            };
        },
    });

    const renderButtons = (items: ToolbarButton[]) =>
        items.map((item) => {
            const buttonState = state?.buttons[allButtons.indexOf(item)];

            return (
                <button
                    key={item.title}
                    type="button"
                    title={item.title}
                    aria-label={item.title}
                    aria-pressed={
                        item.isActive ? buttonState?.isActive : undefined
                    }
                    disabled={buttonState?.isDisabled}
                    onClick={() => editor && item.run(editor)}
                    className={buttonClassName(buttonState?.isActive ?? false)}
                >
                    {item.label}
                </button>
            );
        });

    const fontFamilyValue =
        fontFamilies.find(
            (font) => normalizeFontFamily(font.value) === state?.fontFamily,
        )?.value ?? '';

    return (
        <div className="mt-1 rounded-md border border-stone-300 shadow-sm focus-within:border-mustard-500 focus-within:ring-1 focus-within:ring-mustard-500">
            <div
                role="toolbar"
                aria-label="Formatting"
                className="sticky top-0 z-10 flex flex-wrap items-center gap-1 rounded-t-md border-b border-stone-200 bg-stone-50 p-1.5"
            >
                <select
                    aria-label="Font"
                    className={selectClassName}
                    value={fontFamilyValue}
                    onChange={(e) => {
                        const chain = editor?.chain().focus();

                        if (e.target.value === '') {
                            chain?.unsetFontFamily().run();
                        } else {
                            chain?.setFontFamily(e.target.value).run();
                        }
                    }}
                >
                    {fontFamilies.map((font) => (
                        <option key={font.label} value={font.value}>
                            {font.label}
                        </option>
                    ))}
                </select>
                <select
                    aria-label="Font size"
                    className={selectClassName}
                    value={state?.fontSize ?? ''}
                    onChange={(e) => {
                        const chain = editor?.chain().focus();

                        if (e.target.value === '') {
                            chain?.unsetFontSize().run();
                        } else {
                            chain?.setFontSize(e.target.value).run();
                        }
                    }}
                >
                    {fontSizes.map((size) => (
                        <option key={size} value={size}>
                            {size === '' ? 'Default size' : size}
                        </option>
                    ))}
                </select>
                <Divider />
                {renderButtons(formatButtons)}
                <ColorPicker
                    title="Text colour"
                    icon={
                        <span className="text-sm font-bold leading-none">
                            A
                        </span>
                    }
                    current={state?.color ?? null}
                    colors={textColors}
                    onPick={(color) =>
                        editor?.chain().focus().setColor(color).run()
                    }
                    onClear={() => editor?.chain().focus().unsetColor().run()}
                />
                <ColorPicker
                    title="Highlight"
                    icon={
                        <span className="rounded-sm bg-mustard-200 px-0.5 text-sm font-bold leading-none">
                            H
                        </span>
                    }
                    current={state?.backgroundColor ?? null}
                    colors={highlightColors}
                    onPick={(color) =>
                        editor?.chain().focus().setBackgroundColor(color).run()
                    }
                    onClear={() =>
                        editor?.chain().focus().unsetBackgroundColor().run()
                    }
                />
                <Divider />
                {renderButtons(blockButtons)}
                <Divider />
                {renderButtons(alignButtons)}
                <Divider />
                {renderButtons(historyButtons)}
            </div>

            <EditorContent editor={editor} />
        </div>
    );
}
