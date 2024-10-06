<div id='game-container'>
   <div id="ten-twenty-four">
      <canvas id="game"></canvas>
      <canvas id="HUD"></canvas>
      <script id="shader-fs" type="x-shader/x-fragment">precision mediump float;varying vec2 vtc;varying float vlw;varying vec3 wp;uniform sampler2D uSampler;uniform vec4 lights[48];uniform int nbLights;uniform vec3 ac;uniform int cubeFace,isPlayer,isCurrentPlayer;uniform float it;varying vec3 vp;void main() {vec4 tc=texture2D(uSampler,vec2(vtc.r,vtc.g));vec3 totalLight=vec3(0.,0.,0.);totalLight+=ac;for(int i=0;i<48;i++) {if (i==nbLights) {break;}vec3 light;float d=distance(wp,lights[i].rgb);if (lights[i].a<=1.)light=vec3(sin(it/100.)/2.+.5,sin(it/150.)/2.+.5,cos(it/100.)/2.+.5),light*=max(0.,4.4-.8*d),totalLight+=light*.15;}float profondeur=wp.b/15.;profondeur+=wp.r/50.;if (cubeFace==6)profondeur*=.6;else if (cubeFace==5)profondeur*=.4;if (isPlayer==1) {float distanceS=abs(vtc.r-.5)*2.,distanceT=abs(vtc.g-.5)*2.,distance=max(distanceS,distanceT),alpha=0.;if (distance<.8)tc.rgb=vec3(.2,.2,.2);else alpha=1.-(1.-distance)*3.,tc.rgb=vec3(.2,.2,.2);if (isCurrentPlayer==0)tc.rgb+=vec3(.2,.2,.2)*alpha;else tc.rgb+=vec3(1,.8,.6)*alpha;gl_FragColor=vec4(tc.rgb,1.);} else {if (isCurrentPlayer==1) {vec3 c=tc.rgb*vlw*profondeur,cible=vec3(.1058,.647,.827);c*=cible+vec3(.7,.7,.7);gl_FragColor=vec4(c,tc.a)+vec4(totalLight,tc.a);}else gl_FragColor=vec4(tc.rgb*vlw*profondeur,tc.a)+vec4(totalLight,tc.a);}}</script>
      <script id="lava-fs" type="x-shader/x-fragment">precision mediump float;varying vec2 vtc;varying float vlw;uniform vec2 res;varying vec3 wp;uniform sampler2D uSampler;uniform vec4 lights[48];uniform int nbLights;uniform vec3 ac;uniform int cubeFace;uniform float it;varying vec3 vp;mat2 mm2(in float a) {float c=cos(a),s=sin(a);return mat2(c,-s,s,c);}float noise(in float x) {return texture2D(uSampler,vec2(x*.01,1.),0.).r;}float hash(float n) {return fract(sin(n)*43758.5);}float noise(in vec3 p) {vec3 ip=floor(p),f=fract(p);f=f*f*(3.-2.*f);vec2 uv=ip.rg+vec2(37.,17.)*ip.b+f.rg,rg=texture2D(uSampler,(uv+.5)/256.,0.).gr;return mix(rg.r,rg.g,f.b);}mat3 m3=mat3(0.,.8,.6,-.8,.36,-.48,-.6,-.48,.64);float flow(in vec3 p,in float t) {float time=it/100.0,z=2.,rz=0.;vec3 bp=p;for(float i=1.;i<5.;i++)p+=time*.1,rz+=(sin(noise(p+t*.8)*6.)*.5+.5)/z,p=mix(bp,p,.6),z*=2.,p*=2.01*m3;return rz;}float sins(in float x) {float time=it/100.0,rz=0.,z=2.;for(float i=0.;i<3.;i++)rz+=abs(fract(x*1.4)-.5)/z,x*=1.3,z*=1.15,x-=time*.65*z;return rz;}vec2 iSphere2(in vec3 ro,in vec3 rd) {vec3 oc=ro;float b=dot(oc,rd),c=dot(oc,oc)-1.,h=b*b-c;if (h<0.)return vec2(-1.);return vec2(-b-sqrt(h),-b+sqrt(h));}void main() {vec4 tc=texture2D(uSampler,vec2(vtc.r,vtc.g));gl_FragColor=vec4(tc.rgb,tc.a);float time=it/100.0;float it=time/80.;vec2 p=vp.rg,um=vec2(10.,10.)-.5;vec3 ro=vec3(0.,0.,5.),rd=normalize(vec3(p*.7,-1.5));mat2 mx=mm2(time*.4+um.r*6.),my=mm2(time*.3+um.g*6.);ro.rb*=mx;rd.rb*=mx;ro.rg*=my;rd.rg*=my;vec3 bro=ro,brd=rd,col=vec3(.0125,0.,.025);ro=bro;rd=brd;vec2 sph=iSphere2(ro,rd);float alpha=1.;if (sph.r>0.) {vec3 pos=ro+rd*sph.r,pos2=ro+rd*sph.g,rf=reflect(rd,pos),rf2=reflect(rd,pos2);float nz=-log(abs(flow(rf*1.2,time)-.01)),nz2=-log(abs(flow(rf2*1.2,-time)-.01));col+=(.1*nz*nz*vec3(.12,.12,.5)+.05*nz2*nz2*vec3(.55,.2,.55))*.8;}else alpha=0.,col=vec3(1,0,0);gl_FragColor=vec4(col*8.,alpha);}</script>
      <script id="shader-vs" type="x-shader/x-vertex">attribute vec3 avp;attribute vec2 aTextureCoord;uniform mat4 uMVMatrix,uPMatrix;uniform vec3 cp;varying vec2 vtc;varying float vlw;varying vec3 vp,wp;varying vec2 frag_uv;void main() {gl_Position=uPMatrix*uMVMatrix*vec4(avp,1.),vp=normalize(avp),vtc=aTextureCoord,vlw=1.-gl_Position.b/10.,vlw=1.,wp=cp.rgb+avp;}</script>
   </div>
</div>

<input type="hidden" name="attempts" value="<?= $attempt_count ?>" />

<script src="<?php echo asset_url('assets/js/1024/lost.js'); ?>"></script>

<script>
    function Game() {
        var game = new _1024();

        this.ready = async () => {
            return new Promise((resolve, reject) => {
                resolve(game.ready);
            });
        }

        this.start = async () => {
            return new Promise((resolve, reject) => {
                resolve(game.finished);
            });
        }

        this.end = () => {
            return;
        }
    }
</script>