// China Visa Check widget as a React component.
//   <ChinaVisaCheck />                                  English, passport guessed from the browser language
//   <ChinaVisaCheck lang="ja" passport="jp" theme="dark" />
// lang: en zh tc ko ja ru es de fr ar · passport: two-letter country code · theme: "light" | "dark" | "" (follow the device)
import { useEffect, useRef } from 'react';

const ORIGIN = 'https://chinavisacheck.com';

export default function ChinaVisaCheck({ lang = 'en', passport = '', theme = '', maxWidth = 520 }) {
  const frame = useRef(null);
  const base = `${ORIGIN}${lang === 'en' ? '' : '/' + lang}/china-visa/`;
  const options = [passport.toLowerCase(), theme].filter(Boolean).join('-');

  // The widget reports its height; it answers "cvcH?" in case it loaded before this listener was in place.
  const ask = () => frame.current && frame.current.contentWindow && frame.current.contentWindow.postMessage('cvcH?', ORIGIN);
  useEffect(() => {
    const onMessage = e => {
      if (e.origin === ORIGIN && e.data && e.data.cvcH && frame.current && e.source === frame.current.contentWindow) frame.current.style.height = e.data.cvcH + 'px';
    };
    window.addEventListener('message', onMessage);
    ask();
    return () => window.removeEventListener('message', onMessage);
  }, []);

  return (
    <>
      <iframe ref={frame} src={`${base}embed${options ? '#' + options : ''}`} title="China Visa Check" width="100%" height="320"
        style={{ border: 0, maxWidth }} loading="lazy" onLoad={ask} />
      <p><a href={base}>China Visa Check</a></p>
    </>
  );
}
