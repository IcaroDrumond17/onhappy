//@ts-ignore
import { useToasted } from '@hoppscotch/vue-toasted'
import { ToastPosition } from 'vue-toasted'

interface eventToasted {
  text: string
  clear?: boolean
  actionText?: string
  position?: ToastPosition
}
export function callToasted(args: eventToasted) {
  const toasted = useToasted()

  let text = args.text || 'Sucesso!'
  let clear = args.clear || false
  let actionText = args.actionText || 'OK'
  let position = args.position || 'bottom-right'

  if (clear) toasted.clear()

  toasted.show(text, {
    theme: 'bubble',
    icon: 'fa-check',
    position,
    action: {
      text: actionText,
      class: 'btn-toasted',
      onClick: (e: any, toastObject: { goAway: (arg0: number) => void }) => {
        toastObject.goAway(0)
      },
    },
    style: {
      background: '#ff6b00',
      color: '#fff',
      borderRadius: '6px',
    },
  })
}
